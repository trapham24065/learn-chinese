<?php

namespace App\Http\Controllers;

use App\Services\GuestProgressService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestProgressClaimController extends Controller
{
    public function __construct(
        protected GuestProgressService $guestProgressService
    ) {}

    /**
     * Claim verified guest progress into the authenticated user's account.
     */
    public function claim(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'claim_token' => ['required', 'string'],
        ]);

        try {
            $user = $request->user();
            $result = $this->guestProgressService->claim($user, $validated['claim_token']);

            $status = $result['success'] ? 200 : 422;

            return response()->json($result, $status);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi bảo lưu tiến độ học tập: ' . $e->getMessage(),
            ], 500);
        }
    }
}
