<?php

use App\Models\User;

test('student logged in web guard does not get 403 on admin routes and is redirected to admin login', function () {
    $student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
    ]);

    // Student is logged in via 'web' guard
    $this->actingAs($student, 'web');

    // Accessing admin panel without admin guard session redirects to admin login
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('admin user can access admin panel when authenticated with admin guard', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/dashboard');

    $response->assertSuccessful();
});

test('admin login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertSuccessful();
});
