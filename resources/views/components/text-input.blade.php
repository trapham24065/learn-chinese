@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 bg-slate-50/80 focus:border-[#991b1b] focus:bg-white focus:ring-2 focus:ring-[#991b1b]/10 rounded-2xl shadow-sm text-sm font-medium text-slate-900 transition']) }}>
