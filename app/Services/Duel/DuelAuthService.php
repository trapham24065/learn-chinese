<?php

namespace App\Services\Duel;

use App\Models\DuelRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DuelAuthService
{
    /**
     * Xác định danh tính người chơi (Bắt buộc User đã đăng nhập).
     *
     * @return array{
     *     type: 'user',
     *     user_id: int,
     *     name: string,
     *     avatar: ?string,
     *     ip: ?string
     * }
     * @throws HttpException nếu chưa đăng nhập (401)
     */
    public function resolvePlayerIdentity(Request $request): array
    {
        if (Auth::guard('web')->check()) {
            /** @var User $user */
            $user = Auth::guard('web')->user();
            return [
                'type' => 'user',
                'user_id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ?? null,
                'ip' => $request->ip(),
            ];
        }

        abort(401, 'Vui lòng đăng nhập tài khoản để tham gia Đấu Trường Chinese Duel.');
    }

    /**
     * Xác thực và phân định Slot (1 hoặc 2) của người chơi trong phòng đấu.
     * Tuyệt đối không tin slot do client tự khai báo.
     *
     * @throws HttpException nếu người chơi không thuộc phòng (403)
     */
    public function resolveAuthenticatedSlot(DuelRoom $room, Request $request): int
    {
        if (Auth::guard('web')->check()) {
            $userId = Auth::guard('web')->id();
            if ($room->player1_id === $userId) {
                return 1;
            }
            if ($room->player2_id === $userId) {
                return 2;
            }
        }

        abort(403, 'Bạn không có quyền truy cập hoặc thực hiện thao tác trong phòng đấu này.');
    }
}
