<x-filament-panels::page>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- ─── 📧 Email / SMTP ─────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 shrink-0" style="width: 2.5rem; height: 2.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Hệ thống Email (SMTP)</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Cấu hình gửi email tự động</p>
                    </div>
                </div>
                @if($smtpConfigured)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800 dark:bg-green-950 dark:text-green-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Đã cấu hình
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Chưa cấu hình
                    </span>
                @endif
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Mailer</dt>
                    <dd class="font-mono font-semibold text-gray-900 dark:text-white">{{ strtoupper($mailer) }}</dd>
                </div>
                @if($smtpConfigured)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Host</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $smtpHost }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Port / TLS</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $smtpPort }} / {{ strtoupper($smtpEnc ?: 'none') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">From</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $fromAddress ?: '—' }}</dd>
                </div>
                @else
                <div class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    ⚠️ Đổi <code class="font-mono font-bold">MAIL_MAILER=smtp</code> trong file <code>.env</code> để kích hoạt email thật.
                </div>
                @endif
            </dl>
        </div>

        {{-- ─── 🔊 Azure TTS ────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 shrink-0" style="width: 2.5rem; height: 2.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Azure Text-to-Speech</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Giọng đọc tiếng Trung tự động</p>
                    </div>
                </div>
                @if($ttsConfigured)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800 dark:bg-green-950 dark:text-green-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Đã cấu hình
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 dark:bg-red-950 dark:text-red-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Chưa cấu hình
                    </span>
                @endif
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Provider</dt>
                    <dd class="font-semibold text-gray-900 dark:text-white">Microsoft Azure Speech</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Region</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $ttsRegion ?: '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">API Key</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">
                        {{ $ttsConfigured ? substr($ttsKey, 0, 4) . '••••••••' . substr($ttsKey, -4) : '—' }}
                    </dd>
                </div>
                @if(!$ttsConfigured)
                <div class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    ⚠️ Thêm <code class="font-mono font-bold">AZURE_TTS_KEY</code> và <code class="font-mono font-bold">AZURE_TTS_REGION</code> vào <code>.env</code>.
                </div>
                @endif
            </dl>
        </div>

        {{-- ─── 🗄 Cache & Storage ─────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 shrink-0" style="width: 2.5rem; height: 2.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                        <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Cache & Bộ nhớ</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Trạng thái cache hệ thống</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Cache Driver</dt>
                    <dd class="font-mono font-semibold text-gray-900 dark:text-white">{{ strtoupper($cacheDriver) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Settings Cache</dt>
                    <dd class="font-semibold {{ \Illuminate\Support\Facades\Cache::has('settings.all') ? 'text-green-600' : 'text-gray-400' }}">
                        {{ \Illuminate\Support\Facades\Cache::has('settings.all') ? '✓ Đang cache' : '○ Chưa cache' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Timezone</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $timezone }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-xs text-gray-400">Dùng nút "Xóa cache" ở trên để làm mới toàn bộ cache.</p>
        </div>

        {{-- ─── 🔧 System Info ─────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 shrink-0" style="width: 2.5rem; height: 2.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                        <rect width="16" height="16" x="4" y="4" rx="2"></rect>
                        <rect width="6" height="6" x="9" y="9" rx="1"></rect>
                        <path d="M15 2v2"></path>
                        <path d="M15 20v2"></path>
                        <path d="M2 15h2"></path>
                        <path d="M2 9h2"></path>
                        <path d="M20 15h2"></path>
                        <path d="M20 9h2"></path>
                        <path d="M9 2v2"></path>
                        <path d="M9 20v2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Thông tin hệ thống</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Phiên bản các thành phần</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">PHP</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $phpVersion }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Laravel</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $laravelVersion }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">App Version</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $appVersion }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Environment</dt>
                    <dd class="font-semibold {{ app()->isProduction() ? 'text-green-600' : 'text-amber-600' }}">
                        {{ strtoupper(app()->environment()) }}
                    </dd>
                </div>
            </dl>
        </div>

    </div>

    <x-filament-actions::modals />

</x-filament-panels::page>
