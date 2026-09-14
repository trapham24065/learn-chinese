@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <p class="flex items-center gap-1.5 text-xs font-semibold text-rose-600">
                <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0 text-rose-500"></i>
                <span>{{ $message }}</span>
            </p>
        @endforeach
    </div>
@endif
