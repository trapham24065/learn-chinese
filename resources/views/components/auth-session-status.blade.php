@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 shadow-sm text-emerald-900 transition-all']) }}>
        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-600">
            <i data-lucide="check-circle" class="h-4 w-4"></i>
        </div>
        <p class="text-xs sm:text-sm font-semibold leading-relaxed">
            {{ $status }}
        </p>
    </div>
@endif
