<x-filament-panels::page>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- ─── 📧 Email / SMTP ─────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950">
                        <x-heroicon-o-envelope class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Hệ thống Email (SMTP)</h3>
                        <p class="text-xs text-gray-500">Cấu hình gửi email tự động</p>
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
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950">
                        <x-heroicon-o-speaker-wave class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Azure Text-to-Speech</h3>
                        <p class="text-xs text-gray-500">Giọng đọc tiếng Trung tự động</p>
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
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950">
                    <x-heroicon-o-circle-stack class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Cache & Bộ nhớ</h3>
                    <p class="text-xs text-gray-500">Trạng thái cache hệ thống</p>
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
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-600 dark:bg-gray-800">
                    <x-heroicon-o-cpu-chip class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Thông tin hệ thống</h3>
                    <p class="text-xs text-gray-500">Phiên bản các thành phần</p>
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
