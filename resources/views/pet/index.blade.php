@extends('layouts.app')

@section('title', 'Pet của tôi')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8" x-data="petPage()">

    {{-- Pet Hero Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 mb-6 text-center">
        <div class="text-8xl mb-4 select-none" aria-label="Pet emoji">
            {{ optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->emoji ?? '🥚' }}
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
            {{ $userPet->name ?? $userPet->pet->name }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Giai đoạn {{ $userPet->stage }}:
            {{ optional($userPet->pet->stages->where('stage', $userPet->stage)->first())->name ?? 'Trứng' }}
        </p>

        @if($userPet->status !== 'active')
        <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold
            {{ $userPet->isDormant() ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700' }}">
            {{ $userPet->isDormant() ? '💤 Đang ngủ đông' : '🥚 Dạng trứng' }}
        </span>
        @endif

        {{-- Hunger Bar --}}
        <div class="mt-6">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                <span>No bụng: {{ $userPet->hunger }}/100</span>
                <span>
                    @switch($userPet->getHungerState())
                        @case('happy') 😊 Vui vẻ @break
                        @case('hungry') 🙂 Hơi đói @break
                        @case('very_hungry') 😟 Rất đói @break
                        @case('weak') 😢 Yếu ớt @break
                        @default 💤 Ngủ đông
                    @endswitch
                </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500
                    @if($userPet->hunger >= 70) bg-green-500
                    @elseif($userPet->hunger >= 40) bg-yellow-400
                    @elseif($userPet->hunger >= 20) bg-orange-500
                    @else bg-red-500 @endif"
                     style="width: {{ $userPet->hunger }}%"
                     role="progressbar"
                     aria-valuenow="{{ $userPet->hunger }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
        </div>

        {{-- EXP Progress --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                <span>EXP: {{ $progress['current_exp'] }}</span>
                @if($progress['is_max'])
                    <span>🐉 Đã đạt cấp độ tối đa!</span>
                @else
                    <span>Cần thêm {{ $progress['exp_needed'] }} EXP để tiến hóa</span>
                @endif
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="h-2 rounded-full bg-purple-500 transition-all duration-500"
                     style="width: {{ $progress['percent'] }}%">
                </div>
            </div>
        </div>
    </div>

    {{-- Feed Buttons (active pets only) --}}
    @if($userPet->isActive())
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-gray-900 dark:text-white">🍖 Cho ăn hôm nay</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Còn <strong>{{ $dailyRemaining }}</strong> / 100 XP
            </span>
        </div>

        @if($dailyRemaining <= 0)
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 text-center">
            <p class="text-green-700 dark:text-green-400 font-medium">✅ Pet đã được cho ăn đủ hôm nay!</p>
            <p class="text-green-600 dark:text-green-500 text-sm mt-1">Quay lại ngày mai để tiếp tục.</p>
        </div>
        @else
        <div class="grid grid-cols-4 gap-3">
            @foreach([5, 10, 20, 50] as $amount)
            <button @click="feed({{ $amount }})"
                    :disabled="feeding || {{ $dailyRemaining }} < {{ $amount }}"
                    :class="feeding || {{ $dailyRemaining }} < {{ $amount }}
                        ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'
                        : 'bg-indigo-500 hover:bg-indigo-600 active:scale-95 text-white cursor-pointer'"
                    class="py-3 rounded-xl font-bold transition-all duration-150">
                +{{ $amount }}
            </button>
            @endforeach
        </div>
        @endif

        <p x-show="message"
           x-text="message"
           x-cloak
           class="mt-3 text-center text-sm font-medium"
           :class="isError ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
        </p>
    </div>
    @elseif($userPet->isDormant())
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-2xl p-6 mb-6 text-center">
        <div class="text-4xl mb-2">💤</div>
        <p class="text-amber-800 dark:text-amber-300 font-medium">Pet đang ngủ đông vì bị đói quá lâu.</p>
        <p class="text-amber-700 dark:text-amber-400 text-sm mt-1">Hãy học bài để kiếm XP và cho ăn để đánh thức!</p>
    </div>
    @elseif($userPet->isEgg())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-2xl p-6 mb-6 text-center">
        <div class="text-4xl mb-2">🥚</div>
        <p class="text-red-700 dark:text-red-400 font-medium">Pet đã quay về dạng trứng (lần {{ $userPet->reset_count }}).</p>
        <p class="text-red-600 dark:text-red-500 text-sm mt-1">Hãy học đều đặn để pet không bị đói!</p>
    </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $userPet->exp }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tổng EXP</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ $userPet->total_fed_xp }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tổng XP cho ăn</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $userPet->best_stage }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Giai đoạn cao nhất</p>
        </div>
    </div>

    {{-- Memories --}}
    @if($memories->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
        <h2 class="font-bold text-gray-900 dark:text-white mb-4">📖 Ký ức</h2>
        <div class="space-y-3">
            @foreach($memories as $memory)
            <div class="flex gap-3 text-sm border-b border-gray-100 dark:border-gray-700 pb-3 last:border-0 last:pb-0">
                <span class="text-gray-400 dark:text-gray-500 shrink-0 tabular-nums">
                    {{ $memory->created_at->format('d/m') }}
                </span>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $memory->title }}</p>
                    @if($memory->description)
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ $memory->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function petPage() {
    return {
        feeding: false,
        message: '',
        isError: false,

        feed(amount) {
            if (this.feeding) return;
            this.feeding = true;
            this.message = '';
            this.isError = false;

            const key = 'pet_feed_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);

            fetch('{{ route('pet.feed') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ amount: amount, idempotency_key: key }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.is_duplicate) {
                        this.message = 'Đã xử lý rồi!';
                    } else if (data.stage_up) {
                        this.message = `🎉 Pet tiến hóa! Giai đoạn ${data.new_stage}! +${data.xp_fed} EXP`;
                        setTimeout(() => window.location.reload(), 1800);
                        return;
                    } else {
                        this.message = `+${data.xp_fed} EXP cho pet! 🍖`;
                    }
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    this.isError = true;
                    this.message = data.error ?? 'Có lỗi xảy ra';
                }
            })
            .catch(() => {
                this.isError = true;
                this.message = 'Lỗi kết nối. Vui lòng thử lại.';
            })
            .finally(() => {
                this.feeding = false;
            });
        }
    };
}
</script>
@endpush
@endsection
