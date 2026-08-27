<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#991b1b] border border-transparent rounded-2xl font-bold text-xs text-white uppercase tracking-widest hover:bg-[#7f1d1d] focus:bg-[#7f1d1d] active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#991b1b]/20 transition ease-in-out duration-150 shadow-md shadow-red-950/20']) }}>
    {{ $slot }}
</button>
