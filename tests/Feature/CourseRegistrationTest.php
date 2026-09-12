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
            'current_level' => 'invalid_level',
        ]);

        $response->assertSessionHasErrors(['full_name', 'phone', 'current_level']);
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
}
