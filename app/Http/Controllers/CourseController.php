<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $courses = Course::where('is_active', true)
            ->with(['openClasses'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('courses.index', compact('courses'));
    }

    public function show(string $slug): View
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->with(['openClasses'])
            ->firstOrFail();

        $otherCourses = Course::where('is_active', true)
            ->where('id', '!=', $course->id)
            ->orderBy('sort_order')
            ->take(3)
            ->get();

        return view('courses.show', compact('course', 'otherCourses'));
    }

    public function register(Request $request, string $slug): RedirectResponse
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        // Anti-spam Honeypot: bots fill hidden field website_url_hp
        if (! empty($request->input('website_url_hp'))) {
            return redirect()->route('courses.index');
        }

        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9\+\s\.\(\)\-]+$/'],
            'course_class_id' => ['nullable', 'exists:course_classes,id'],
            'current_level' => ['required', 'string', 'in:chua_biet_gi,co_ban_phat_am,hsk1_2,hsk3_4,giao_tiep'],
            'preferred_schedule' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'zalo' => ['nullable', 'string', 'max:50'],
            'learning_goal' => ['nullable', 'string', 'max:1000'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'phone.required' => 'Vui lòng nhập số điện thoại để giáo viên liên hệ tư vấn.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'current_level.required' => 'Vui lòng chọn trình độ hiện tại của bạn.',
        ]);

        $phoneNormalized = CourseRegistration::normalizePhone($validated['phone']);

        // Anti-spam duplicate check within 5 minutes
        $recent = CourseRegistration::where('course_id', $course->id)
            ->where(function ($q) use ($phoneNormalized, $request) {
                $q->where('phone_normalized', $phoneNormalized)
                  ->orWhere('ip_address', $request->ip());
            })
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recent) {
            return redirect()->route('courses.success', ['code' => $recent->registration_code])
                ->with('info', 'Bạn đã gửi đăng ký gần đây. Giáo viên sẽ liên hệ với bạn trong thời gian sớm nhất!');
        }

        $registration = CourseRegistration::create([
            'course_id' => $course->id,
            'course_class_id' => $validated['course_class_id'] ?? null,
            'user_id' => auth()->id(),
            'full_name' => trim($validated['full_name']),
            'phone' => trim($validated['phone']),
            'phone_normalized' => $phoneNormalized,
            'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
            'zalo' => ! empty($validated['zalo']) ? trim($validated['zalo']) : null,
            'current_level' => $validated['current_level'],
            'preferred_schedule' => $validated['preferred_schedule'] ?? null,
            'learning_goal' => $validated['learning_goal'] ?? null,
            'payment_amount' => $course->price,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $registration->recordActivity(
            type: 'created',
            description: "Học viên đăng ký trực tuyến từ website (Khóa: {$course->title})"
        );

        return redirect()->route('courses.success', ['code' => $registration->registration_code])
            ->with('success', 'Đăng ký thành công! Vui lòng lưu lại mã đăng ký hoặc chuyển khoản để giữ chỗ.');
    }

    public function success(string $code): View
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $registration = CourseRegistration::where('registration_code', $code)
            ->with(['course', 'courseClass'])
            ->firstOrFail();

        $bankId = setting('bank_id', 'MB');
        $bankAccount = setting('bank_account', '0988888888');
        $bankAccountName = setting('bank_account_name', 'TIENG TRUNG CO GIAO');
        $vietQrUrl = $registration->getVietQrUrl();

        return view('courses.success', compact('registration', 'bankId', 'bankAccount', 'bankAccountName', 'vietQrUrl'));
    }
}
