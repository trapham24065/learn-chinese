<?php

namespace App\Console\Commands;

use App\Models\DuelRoom;
use App\Services\Duel\DuelGameEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncStaleDuelRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duel:sync-stale-rooms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ và giải cứu các phòng đấu 1v1 bị treo do người chơi mất kết nối hoặc đóng tab.';

    /**
     * Execute the console command.
     */
    public function handle(DuelGameEngine $engine): int
    {
        $now = microtime(true);

        // 1. Quét các phòng countdown hoặc in_progress đã quá hạn phase_ends_at hoặc cả hai mất kết nối > 40s
        $staleRooms = DuelRoom::whereIn('status', ['countdown', 'in_progress'])
            ->where(function ($query) use ($now) {
                $query->where('phase_ends_at', '<', $now)
                    ->orWhere(function ($q) use ($now) {
                        $q->whereNotNull('player1_last_seen_at')
                            ->whereNotNull('player2_last_seen_at')
                            ->where('player1_last_seen_at', '<', $now - 40.0)
                            ->where('player2_last_seen_at', '<', $now - 40.0);
                    });
            })
            ->get();

        $processedCount = 0;
        foreach ($staleRooms as $room) {
            DB::transaction(function () use ($room, $engine, $now, &$processedCount) {
                $locked = DuelRoom::where('id', $room->id)->lockForUpdate()->first();
                if ($locked && in_array($locked->status, ['countdown', 'in_progress'], true)) {
                    $engine->advanceStateLocked($locked, $now);
                    $engine->checkDisconnectionAndForfeit($locked, $now);
                    $locked->save();
                    $processedCount++;
                }
            });
        }

        // 2. Dọn các phòng waiting quá 15 phút không ai tham gia
        $expiredWaiting = DuelRoom::where('status', 'waiting')
            ->where('created_at', '<', now()->subMinutes(15))
            ->update(['status' => 'abandoned']);

        $this->info("Đã xử lý {$processedCount} phòng bị treo và dọn dẹp {$expiredWaiting} phòng chờ quá hạn.");
        return Command::SUCCESS;
    }
}
