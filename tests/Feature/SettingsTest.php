<?php

use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->seed(SettingSeeder::class);
    Cache::forget('settings.all');
});

test('settings seeder creates all required default settings', function () {
    expect(Setting::where('key', 'site_name')->exists())->toBeTrue();
    expect(Setting::where('key', 'maintenance_mode')->exists())->toBeTrue();
    expect(Setting::where('key', 'allow_registration')->exists())->toBeTrue();
    expect(Setting::where('key', 'announcement_enabled')->exists())->toBeTrue();
    expect(Setting::where('key', 'flashcard_daily_limit')->exists())->toBeTrue();
    expect(Setting::where('key', 'feature_flashcards')->exists())->toBeTrue();
});

test('setting and setting_bool helper functions work with defaults', function () {
    expect(setting('site_name'))->toBe('Learn Chinese');
    expect(setting('non_existent_key', 'fallback'))->toBe('fallback');
    expect(setting_bool('allow_registration'))->toBeTrue();
    expect(setting_bool('maintenance_mode'))->toBeFalse();
    expect(setting_bool('non_existent_key', false))->toBeFalse();
});

test('settings service set updates db and immediately clears cache', function () {
    $svc = app(SettingsService::class);

    // Warm cache
    expect($svc->get('site_name'))->toBe('Learn Chinese');

    // Update
    $svc->set('site_name', 'Học Tiếng Trung Mới');

    // Direct helper call should reflect new value without stale cache
    expect(setting('site_name'))->toBe('Học Tiếng Trung Mới');
    expect(Setting::where('key', 'site_name')->value('value'))->toBe('Học Tiếng Trung Mới');
});

test('guest gets normal page when maintenance mode is off', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('guest gets 503 maintenance page when maintenance mode is enabled', function () {
    app(SettingsService::class)->set('maintenance_mode', '1');

    $response = $this->get('/');
    $response->assertStatus(503);
    $response->assertSee('Website đang được nâng cấp');
});

test('admin bypasses maintenance mode', function () {
    app(SettingsService::class)->set('maintenance_mode', '1');

    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    // Admin accessing site gets 200 (bypasses 503 maintenance)
    $response = $this->actingAs($admin)->get('/flashcards');
    $response->assertStatus(200);
});

test('registration view displays closed message when allow_registration is disabled', function () {
    app(SettingsService::class)->set('allow_registration', '0');

    $response = $this->get('/register');
    $response->assertStatus(200);
    $response->assertSee('Đăng ký tạm thời đóng');

    // Attempt to register via POST should be rejected
    $postResponse = $this->post('/register', [
        'name' => 'Học Viên Mới',
        'email' => 'newstudent@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $postResponse->assertSessionHasErrors('email');
    expect(User::where('email', 'newstudent@example.com')->exists())->toBeFalse();
});

test('announcement banner is rendered when enabled with content', function () {
    $svc = app(SettingsService::class);
    $svc->set('announcement_enabled', '1');
    $svc->set('announcement_title', 'Thông báo bảo trì định kỳ');
    $svc->set('announcement_content', 'Hệ thống sẽ cập nhật lúc 23:00 tối nay.');

    $response = $this->get('/');
    $response->assertStatus(200);
    $response->assertSee('Thông báo bảo trì định kỳ');
    $response->assertSee('Hệ thống sẽ cập nhật lúc 23:00 tối nay.');
});

test('admin can access manage settings page', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $response = $this->actingAs($admin, 'admin')->get('/admin/manage-settings');
    $response->assertSuccessful();
    $response->assertSee('Cài đặt chung');
    $response->assertSee('Thông tin Website');
});

test('admin can access system tools page', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $response = $this->actingAs($admin, 'admin')->get('/admin/system-tools');
    $response->assertSuccessful();
    $response->assertSee('Công cụ hệ thống');
});

