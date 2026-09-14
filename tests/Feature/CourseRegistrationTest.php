<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseRegistration;
use App\Models\CourseRegistrationActivity;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected Course $course;
    protected CourseClass $class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->course = Course::create([
            'title' => 'Tiếng Trung Toàn Diện HSK 1 - 2',
            'slug' => 'tieng-trung-toan-dien-hsk-1-2',
            'category' => 'hsk_starter',
            'summary' => 'Khóa học nền tảng cho người mới bắt đầu',
            'price' => 1950000,
            'original_price' => 2800000,
            'duration_weeks' => 8,
            'total_sessions' => 24,
            'highlights' => ['Sửa phát âm 1:1 qua Google Meet', 'Xem lại video buổi học'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->class = CourseClass::create([
            'course_id' => $this->course->id,
            'name' => 'Lớp T10 - Tối 2-4-6 (19:30)',
            'code' => 'HSK12-2610A',
            'start_date' => '2026-10-05',
            'schedule_days' => 'Thứ 2 - 4 - 6',
            'schedule_time' => '19:30 - 21:00',
            'max_students' => 12,
            'status' => 'open',
            'meet_url' => 'https://meet.google.com/private-test-link',
        ]);
    }

    public function test_courses_index_page_loads_successfully_with_active_courses(): void
    {
        $response = $this->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertSee('Tiếng Trung Toàn Diện HSK 1 - 2');
        $response->assertSee('1.950.000');
        $response->assertSee('Lớp T10 - Tối 2-4-6 (19:30)');
    }

    public function test_course_show_page_loads_successfully_and_hides_private_meet_url(): void
    {
        $response = $this->get(route('courses.show', $this->course->slug));

        $response->assertStatus(200);
        $response->assertSee($this->course->title);
        $response->assertSee('Lớp T10 - Tối 2-4-6 (19:30)');
        $response->assertSee('Được cung cấp bản ghi buổi học để xem lại trong suốt khóa học và thời gian hỗ trợ');
        // Critical: Meet URL must NEVER be exposed publicly on frontend
        $response->assertDontSee('https://meet.google.com/private-test-link');
    }

    public function test_courses_returns_404_when_feature_flag_is_disabled(): void
    {
        app(SettingsService::class)->set('feature_courses', '0');

        $response = $this->get(route('courses.index'));
        $response->assertStatus(404);

        $showResponse = $this->get(route('courses.show', $this->course->slug));
        $showResponse->assertStatus(404);
    }

    public function test_student_can_register_for_course_successfully(): void
    {
        $response = $this->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Nguyễn Thị Mai',
            'phone' => '0987 654 321',
            'course_class_id' => $this->class->id,
            'current_level' => 'chua_biet_gi',
            'preferred_schedule' => 'Tối 2-4-6',
            'email' => 'mai.nguyen@example.com',
            'zalo' => '0987654321',
            'learning_goal' => 'Muốn đi du học Trung Quốc',
        ]);

        $registration = CourseRegistration::where('phone_normalized', '0987654321')->first();

        $this->assertNotNull($registration);
        $this->assertEquals('Nguyễn Thị Mai', $registration->full_name);
        $this->assertEquals($this->course->id, $registration->course_id);
        $this->assertEquals($this->class->id, $registration->course_class_id);
        $this->assertEquals('pending', $registration->status);
        $this->assertEquals('unpaid', $registration->payment_status);
        $this->assertNotEmpty($registration->registration_code);
        $this->assertEquals(1950000, (float) $registration->payment_amount);

        // Check that activity was logged
        $this->assertDatabaseHas('course_registration_activities', [
            'course_registration_id' => $registration->id,
            'type' => 'created',
        ]);

        $response->assertRedirect(route('courses.success', ['code' => $registration->registration_code]));
    }

    public function test_honeypot_drops_spam_bot_submission(): void
    {
        $initialCount = CourseRegistration::count();

        $response = $this->post(route('courses.register', $this->course->slug), [
            'website_url_hp' => 'http://spambot-link.com',
            'full_name' => 'Bot Spammer',
            'phone' => '0999999999',
            'current_level' => 'chua_biet_gi',
        ]);

        $this->assertEquals($initialCount, CourseRegistration::count());
        $response->assertRedirect(route('courses.index'));
    }

    public function test_registration_validation_requires_mandatory_fields(): void
    {
        $response = $this->post(route('courses.register', $this->course->slug), [
            'full_name' => '',
            'phone' => '',
            'email' => '',
            'current_level' => 'invalid_level',
        ]);

        $response->assertSessionHasErrors(['full_name', 'phone', 'email', 'current_level']);
    }

    public function test_guest_registration_automatically_creates_user_account_with_phone_password_and_logs_in(): void
    {
        $response = $this->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Lê Hoàng Nam',
            'phone' => '0912 888 999',
            'email' => 'hoangnam@example.com',
            'current_level' => 'chua_biet_gi',
        ]);

        $registration = CourseRegistration::where('phone_normalized', '0912888999')->first();
        $this->assertNotNull($registration);

        // Check user was created
        $user = \App\Models\User::where('email', 'hoangnam@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Lê Hoàng Nam', $user->name);
        $this->assertEquals($user->id, $registration->user_id);

        // Check password is phone normalized
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('0912888999', $user->password));

        // Check user is automatically authenticated!
        $this->assertAuthenticatedAs($user);

        // Check success page has account details
        $response->assertRedirect(route('courses.success', $registration->registration_code));
        $successResponse = $this->get(route('courses.success', $registration->registration_code));
        $successResponse->assertSee('hoangnam@example.com');
        $successResponse->assertSee('0912888999');
        $successResponse->assertSee('Tự động kích hoạt tài khoản');
    }

    public function test_guest_registration_with_existing_email_links_registration_without_overwriting_password(): void
    {
        $existingUser = \App\Models\User::factory()->create([
            'email' => 'da.co.tk@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);

        $response = $this->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Tên Mới',
            'phone' => '0933 444 555',
            'email' => 'da.co.tk@example.com',
            'current_level' => 'hsk1_2',
        ]);

        $registration = CourseRegistration::where('phone_normalized', '0933444555')->first();
        $this->assertNotNull($registration);
        $this->assertEquals($existingUser->id, $registration->user_id);

        // Password should NOT be overwritten
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret123', $existingUser->fresh()->password));
    }

    public function test_success_page_displays_registration_details_and_vietqr(): void
    {
        $registration = CourseRegistration::create([
            'course_id' => $this->course->id,
            'course_class_id' => $this->class->id,
            'full_name' => 'Trần Văn Hùng',
            'phone' => '0912345678',
            'phone_normalized' => '0912345678',
            'current_level' => 'co_ban_phat_am',
            'payment_amount' => $this->course->price,
        ]);

        $response = $this->get(route('courses.success', $registration->registration_code));

        $response->assertStatus(200);
        $response->assertSee('Trần Văn Hùng');
        $response->assertSee($registration->registration_code);
        $response->assertSee('img.vietqr.io');
    }

    public function test_authenticated_user_id_is_saved_on_registration(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Học Viên Có Tài Khoản',
            'phone' => '0901111222',
            'current_level' => 'hsk1_2',
            'email' => $user->email,
        ]);

        $registration = CourseRegistration::where('phone_normalized', '0901111222')->first();

        $this->assertNotNull($registration);
        $this->assertEquals($user->id, $registration->user_id);
    }

    public function test_my_courses_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get(route('courses.my'));

        $response->assertRedirect(route('login'));
    }

    public function test_my_courses_shows_registrations_for_authenticated_user(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
            'email' => 'student@example.com',
        ]);

        // Registration linked via user_id
        CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => $user->id,
            'full_name' => 'Test User',
            'phone' => '0900000001',
            'current_level' => 'chua_biet_gi',
            'payment_amount' => $this->course->price,
        ]);

        // Registration linked via email (no user_id)
        CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => null,
            'email' => 'student@example.com',
            'full_name' => 'Test User Email',
            'phone' => '0900000002',
            'current_level' => 'chua_biet_gi',
            'payment_amount' => $this->course->price,
        ]);

        // Registration belonging to someone else
        CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => null,
            'email' => 'other@example.com',
            'full_name' => 'Other User',
            'phone' => '0900000003',
            'current_level' => 'chua_biet_gi',
            'payment_amount' => $this->course->price,
        ]);

        $response = $this->actingAs($user)->get(route('courses.my'));

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('Test User Email');
        $response->assertDontSee('Other User');
    }

    public function test_claim_registration_succeeds_with_matching_email(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
            'email' => 'claim-test@example.com',
        ]);

        $registration = CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => null,
            'email' => 'claim-test@example.com',
            'full_name' => 'Người Claim',
            'phone' => '0900111222',
            'current_level' => 'chua_biet_gi',
            'payment_amount' => $this->course->price,
        ]);

        $response = $this->actingAs($user)->post(
            route('courses.claim', $registration->registration_code)
        );

        $response->assertRedirect(route('courses.my'));
        $response->assertSessionHas('success');
        $this->assertEquals($user->id, $registration->fresh()->user_id);
    }

    public function test_claim_registration_fails_with_wrong_email(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
            'email' => 'wrong-email@example.com',
        ]);

        $registration = CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => null,
            'email' => 'correct-email@example.com',
            'full_name' => 'Người Khác',
            'phone' => '0900333444',
            'current_level' => 'chua_biet_gi',
            'payment_amount' => $this->course->price,
        ]);

        $response = $this->actingAs($user)->post(
            route('courses.claim', $registration->registration_code)
        );

        $response->assertSessionHas('error');
        $this->assertNull($registration->fresh()->user_id);
    }

    public function test_duplicate_registration_for_same_course_redirects_to_existing_order(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
            'email' => 'duplicate.tester@example.com',
        ]);

        $existing = CourseRegistration::create([
            'course_id' => $this->course->id,
            'user_id' => $user->id,
            'full_name' => 'Duplicate Tester',
            'phone' => '0912345678',
            'email' => 'duplicate.tester@example.com',
            'current_level' => 'chua_biet_gi',
            'status' => 'pending',
            'payment_amount' => $this->course->price,
        ]);

        $initialCount = CourseRegistration::count();

        // Attempting to register again for the same course with the same user
        $response = $this->actingAs($user)->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Duplicate Tester',
            'phone' => '0912345678',
            'email' => 'duplicate.tester@example.com',
            'current_level' => 'co_ban_phat_am',
        ]);

        $response->assertRedirect(route('courses.success', ['code' => $existing->registration_code]));
        $response->assertSessionHas('info');
        $this->assertEquals($initialCount, CourseRegistration::count());
    }

    public function test_registration_blocks_user_exceeding_pending_limit(): void
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
            'email' => 'quota.tester@example.com',
        ]);

        // Create 3 other courses and 3 pending registrations
        for ($i = 1; $i <= 3; $i++) {
            $c = Course::create([
                'title' => "Khóa thử nghiệm $i",
                'slug' => "khoa-thu-nghiem-$i",
                'category' => 'hsk_starter',
                'summary' => 'Tóm tắt',
                'price' => 1000000,
                'is_active' => true,
            ]);

            CourseRegistration::create([
                'course_id' => $c->id,
                'user_id' => $user->id,
                'full_name' => 'Quota Tester',
                'phone' => '0933445566',
                'email' => 'quota.tester@example.com',
                'current_level' => 'chua_biet_gi',
                'status' => 'pending',
                'payment_amount' => 1000000,
                'created_at' => now()->subHour(),
            ]);
        }

        // Try registering 4th course -> should be blocked by anti-spam quota
        $response = $this->actingAs($user)->post(route('courses.register', $this->course->slug), [
            'full_name' => 'Quota Tester',
            'phone' => '0933445566',
            'email' => 'quota.tester@example.com',
            'current_level' => 'chua_biet_gi',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    public function test_anti_spam_time_gate_drops_lightning_fast_bot_submissions(): void
    {
        // Token encrypted at current time (0 seconds elapsed) -> should drop silently
        $freshToken = encrypt(time());

        $initialCount = CourseRegistration::count();

        $response = $this->post(route('courses.register', $this->course->slug), [
            '_rendered_at' => $freshToken,
            'full_name' => 'Speed Bot',
            'phone' => '0988776655',
            'email' => 'bot@example.com',
            'current_level' => 'chua_biet_gi',
        ]);

        $response->assertRedirect(route('courses.index'));
        $this->assertEquals($initialCount, CourseRegistration::count());
    }
}

