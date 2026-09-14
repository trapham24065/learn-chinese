<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'email' => ['required', 'email', 'max:255'],
            'course_class_id' => ['nullable', 'exists:course_classes,id'],
            'current_level' => ['required', 'string', 'in:chua_biet_gi,co_ban_phat_am,hsk1_2,hsk3_4,giao_tiep'],
            'preferred_schedule' => ['nullable', 'string', 'max:255'],
            'zalo' => ['nullable', 'string', 'max:50'],
            'learning_goal' => ['nullable', 'string', 'max:1000'],
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'phone.required' => 'Vui lòng nhập số điện thoại để giáo viên liên hệ tư vấn.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'email.required' => 'Vui lòng nhập địa chỉ email để nhận link lớp học và tài khoản học.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'current_level.required' => 'Vui lòng chọn trình độ hiện tại của bạn.',
        ]);

        $phoneNormalized = CourseRegistration::normalizePhone($validated['phone']);
        $email = strtolower(trim($validated['email']));

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

        // Auto-provisioning: Tự động khởi tạo hoặc liên kết tài khoản
        $userId = auth()->id();
        $autoAccountCreated = false;
        $existingAccountLinked = false;

        if (! $userId) {
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $userId = $existingUser->id;
                $existingAccountLinked = true;
            } else {
                $newUser = new User();
                $newUser->name = trim($validated['full_name']);
                $newUser->email = $email;
                $newUser->password = Hash::make($phoneNormalized);
                $newUser->role = User::ROLE_STUDENT;
                $newUser->email_verified_at = now();
                $newUser->save();

                $userId = $newUser->id;
                $autoAccountCreated = true;

                // Tự động đăng nhập cho học viên mới
                Auth::login($newUser);
            }
        }

        $registration = CourseRegistration::create([
            'course_id' => $course->id,
            'course_class_id' => $validated['course_class_id'] ?? null,
            'user_id' => $userId,
            'full_name' => trim($validated['full_name']),
            'phone' => trim($validated['phone']),
            'phone_normalized' => $phoneNormalized,
            'email' => $email,
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
            description: "Học viên đăng ký trực tuyến từ website (Khóa: {$course->title})",
            userId: $userId
        );

        if ($autoAccountCreated) {
            $registration->recordActivity(
                type: 'account_created',
                description: "Tự động tạo tài khoản học viên ({$email}) với mật khẩu mặc định là Số điện thoại",
                userId: $userId
            );
        }

        if ($autoAccountCreated) {
            session()->flash('auto_account_created', [
                'email' => $email,
                'password' => $phoneNormalized,
            ]);
        } elseif ($existingAccountLinked) {
            session()->flash('existing_account_linked', [
                'email' => $email,
            ]);
        }

        return redirect()->route('courses.success', ['code' => $registration->registration_code])
            ->with('success', 'Đăng ký thành công! Vui lòng lưu lại thông tin tài khoản và mã đăng ký.');
    }

    public function success(string $code): View
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $registration = CourseRegistration::where('registration_code', $code)
            ->with(['course', 'courseClass', 'user'])
            ->firstOrFail();

        $bankId = setting('bank_id', 'MB');
        $bankAccount = setting('bank_account', '0988888888');
        $bankAccountName = setting('bank_account_name', 'TIENG TRUNG CO GIAO');
        $vietQrUrl = $registration->getVietQrUrl();

        return view('courses.success', compact('registration', 'bankId', 'bankAccount', 'bankAccountName', 'vietQrUrl'));
    }

    public function myRegistrations(): View
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $user = auth()->user();

        $registrations = CourseRegistration::forUser($user)
            ->with(['course', 'courseClass'])
            ->orderByDesc('created_at')
            ->get();

        return view('courses.my-registrations', compact('registrations'));
    }

    public function claimRegistration(Request $request, string $code): \Illuminate\Http\RedirectResponse
    {
        abort_unless(setting_bool('feature_courses', true), 404);

        $user = auth()->user();

        $registration = CourseRegistration::where('registration_code', $code)
            ->whereNull('user_id')
            ->firstOrFail();

        // Xác minh email khớp
        if (! $user->email || strtolower($registration->email ?? '') !== strtolower($user->email)) {
            return back()->with('error', 'Email tài khoản của bạn không khớp với đơn đăng ký. Vui lòng kiểm tra lại.');
        }

        $registration->update(['user_id' => $user->id]);

        $registration->recordActivity(
            type: 'account_linked',
            description: "Học viên đã liên kết đơn đăng ký với tài khoản: {$user->email}",
            userId: $user->id
        );

        return redirect()->route('courses.my')
            ->with('success', 'Đã liên kết đơn đăng ký vào tài khoản của bạn thành công!');
    }
}
