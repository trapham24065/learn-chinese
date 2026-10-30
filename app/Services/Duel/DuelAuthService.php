<?php

namespace App\Services\Duel;

use App\Models\DuelRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DuelAuthService
{
    /**
     * Xác định danh tính người chơi (User đã đăng nhập hoặc Khách vãng lai).
     *
     * @return array{
     *     type: 'user'|'guest',
     *     user_id: ?int,
     *     name: string,
     *     avatar: ?string,
     *     guest_token_plain: ?string,
     *     guest_token_hash: ?string
     * }
     */
    public function resolvePlayerIdentity(Request $request, ?string $roomCode = null): array
    {
        // 1. Nếu là User đã đăng nhập trên web guard
        if (Auth::guard('web')->check()) {
            /** @var User $user */
            $user = Auth::guard('web')->user();
            return [
                'type' => 'user',
                'user_id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ?? null,
                'guest_token_plain' => null,
                'guest_token_hash' => null,
            ];
        }

        // 2. Nếu là Khách vãng lai (Guest)
        $cookieName = $roomCode ? "duel_guest_token_{$roomCode}" : null;
        $existingToken = $cookieName ? $request->cookie($cookieName) : null;

        if ($existingToken && is_string($existingToken) && strlen($existingToken) >= 32) {
            $plainToken = $existingToken;
        } else {
            // Sinh token bảo mật ngẫu nhiên
            $plainToken = Str::random(40) . microtime(true);
        }

        $tokenHash = hash_hmac('sha256', $plainToken, config('app.key'));
        $shortId = strtoupper(substr($tokenHash, 0, 4));

        return [
            'type' => 'guest',
            'user_id' => null,
            'name' => $request->input('guest_name') ? e(substr($request->input('guest_name'), 0, 30)) : "Khách #{$shortId}",
            'avatar' => null,
            'guest_token_plain' => $plainToken,
            'guest_token_hash' => $tokenHash,
        ];
    }

    /**
     * Xác thực và phân định Slot (1 hoặc 2) của người chơi trong phòng đấu.
     * Tuyệt đối không tin slot do client tự khai báo.
     *
     * @throws HttpException nếu người chơi không thuộc phòng (403)
     */
    public function resolveAuthenticatedSlot(DuelRoom $room, Request $request): int
    {
        // Kiểm tra User đăng nhập
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
            if ($room->player1_type === 'user' && $room->player1_id === $userId) {
                return 1;
            }
            if ($room->player2_type === 'user' && $room->player2_id === $userId) {
                return 2;
            }
        }

        // Kiểm tra Guest qua cookie scoped theo phòng
        $cookieName = "duel_guest_token_{$room->room_code}";
        $guestToken = $request->cookie($cookieName);

        if ($guestToken && is_string($guestToken)) {
            $tokenHash = hash_hmac('sha256', $guestToken, config('app.key'));
            if ($room->player1_type === 'guest' && $room->player1_guest_token_hash === $tokenHash) {
                return 1;
            }
            if ($room->player2_type === 'guest' && $room->player2_guest_token_hash === $tokenHash) {
                return 2;
            }
        }

        // Kiểm tra token truyền qua header phòng trường hợp cookie bị chặn trên WebView mobile
        $headerToken = $request->header('X-Duel-Guest-Token');
        if ($headerToken && is_string($headerToken)) {
            $tokenHash = hash_hmac('sha256', $headerToken, config('app.key'));
            if ($room->player1_type === 'guest' && $room->player1_guest_token_hash === $tokenHash) {
                return 1;
            }
            if ($room->player2_type === 'guest' && $room->player2_guest_token_hash === $tokenHash) {
                return 2;
            }
        }

        abort(403, 'Bạn không có quyền truy cập hoặc thực hiện thao tác trong phòng đấu này.');
    }

    /**
     * Tạo cookie đính kèm cho Guest token scoped theo mã phòng.
     */
    public function makeGuestCookie(string $roomCode, string $plainToken): \Symfony\Component\HttpFoundation\Cookie
    {
        return cookie(
            name: "duel_guest_token_{$roomCode}",
            value: $plainToken,
            minutes: 180, // 3 giờ
            path: '/',
            secure: config('session.secure', false),
            httpOnly: true,
            sameSite: 'lax'
        );
    }
}
