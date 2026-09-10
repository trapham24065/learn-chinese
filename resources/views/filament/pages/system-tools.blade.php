<x-filament-panels::page>

<div style="display: flex; flex-direction: column; gap: 1.75rem; padding-bottom: 2.5rem;">

    {{-- ══ 1. HERO BANNER ══ --}}
    <div style="
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 42%, #831843 82%, #991b1b 100%);
        color: white;
        padding: 2rem 2.25rem;
        box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.1);
    ">
        {{-- Background Decorative Hanzi Watermark --}}
        <div style="
            position: absolute;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11rem;
            font-weight: 900;
            opacity: 0.05;
            line-height: 1;
            user-select: none;
            pointer-events: none;
            color: white;
        ">统</div>

        <div style="position: relative; z-index: 1;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(251, 191, 36, 0.15); border: 1px solid rgba(251, 191, 36, 0.35); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: #fde68a;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                <span>Hạ tầng & Dịch vụ · System Operations & Diagnostics</span>
            </div>

            <h1 style="font-size: 1.85rem; font-weight: 900; letter-spacing: -0.02em; margin-top: 0.75rem; line-height: 1.2;">
                Bảng Điều Khiển Công Cụ Hệ Thống 🛠️
            </h1>
            
            <p style="margin-top: 0.5rem; font-size: 0.875rem; color: rgba(255, 255, 255, 0.82); max-width: 44rem; line-height: 1.6;">
                Theo dõi tình trạng sẵn sàng của các dịch vụ quan trọng (SMTP Email, Microsoft Azure TTS), quản lý bộ nhớ đệm ứng dụng và kiểm tra thông số môi trường máy chủ.
            </p>
        </div>
    </div>

    {{-- ══ 2. FOUR DIAGNOSTIC BENTO CARDS ══ --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">

        {{-- ─── Card 1: 📧 Email / SMTP ─────────────────────────────────── --}}
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="height: 4px; background: linear-gradient(90deg, #2563eb, #60a5fa); width: 100%;"></div>
            
            <div style="padding: 1.5rem 1.75rem;">
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="height: 2.75rem; width: 2.75rem; border-radius: 0.85rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">Hệ thống Email</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Cấu hình gửi email tự động (SMTP)</p>
                        </div>
                    </div>

                    @if($smtpConfigured)
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; white-space: nowrap;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.25);"></span>
                            Đã cấu hình
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; white-space: nowrap;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626;"></span>
                            Chưa cấu hình
                        </span>
                    @endif
                </div>

                {{-- Key-Value Details --}}
                <div style="display: flex; flex-direction: column; gap: 0.55rem; background: #f8fafc; border-radius: 0.85rem; padding: 1rem; border: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Giao thức (Mailer):</span>
                        <span style="font-family: monospace; font-weight: 700; color: #0f172a; background: white; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">{{ strtoupper($mailer) }}</span>
                    </div>

                    @if($smtpConfigured)
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Máy chủ (Host):</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a;">{{ $smtpHost }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Cổng / Mã hóa:</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a;">{{ $smtpPort }} ({{ strtoupper($smtpEnc ?: 'NONE') }})</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Email người gửi:</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a;">{{ $fromAddress ?: '—' }}</span>
                    </div>
                    @else
                    <div style="margin-top: 4px; padding: 8px 10px; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; font-size: 0.75rem; color: #92400e; line-height: 1.5;">
                        ⚠️ Đổi <code style="font-weight: 700; background: rgba(0,0,0,0.05); padding: 1px 4px; border-radius: 4px;">MAIL_MAILER=smtp</code> trong file <code>.env</code> để gửi thư thật.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer Action Button --}}
            <div style="padding: 1rem 1.75rem; border-top: 1px solid #f1f5f9; background: #fafafa;">
                <button type="button" wire:click="mountAction('sendTestEmail')"
                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2563eb; color: white; border: none; border-radius: 0.75rem; padding: 0.7rem 1rem; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                        <line x1="22" x2="11" y1="2" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span>Gửi Thư Kiểm Tra (Test Email)</span>
                </button>
            </div>
        </div>

        {{-- ─── Card 2: 🔊 Azure Text-to-Speech ─────────────────────────── --}}
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="height: 4px; background: linear-gradient(90deg, #8b5cf6, #c084fc); width: 100%;"></div>
            
            <div style="padding: 1.5rem 1.75rem;">
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="height: 2.75rem; width: 2.75rem; border-radius: 0.85rem; background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #8b5cf6; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">Azure Text-to-Speech</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Giọng đọc tiếng Trung tự nhiên</p>
                        </div>
                    </div>

                    @if($ttsConfigured)
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; white-space: nowrap;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.25);"></span>
                            Sẵn sàng
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; white-space: nowrap;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626;"></span>
                            Chưa cấu hình
                        </span>
                    @endif
                </div>

                {{-- Key-Value Details --}}
                <div style="display: flex; flex-direction: column; gap: 0.55rem; background: #f8fafc; border-radius: 0.85rem; padding: 1rem; border: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Nhà cung cấp:</span>
                        <span style="font-weight: 700; color: #0f172a;">Microsoft Azure Speech</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Khu vực (Region):</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a; background: white; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">{{ $ttsRegion ?: 'Chưa đặt' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">API Key:</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a;">
                            {{ $ttsConfigured ? substr($ttsKey, 0, 4) . '••••••••' . substr($ttsKey, -4) : '—' }}
                        </span>
                    </div>

                    @if(!$ttsConfigured)
                    <div style="margin-top: 4px; padding: 8px 10px; border-radius: 8px; background: #fffbeb; border: 1px solid #fef3c7; font-size: 0.75rem; color: #92400e; line-height: 1.5;">
                        ⚠️ Thêm <code style="font-weight: 700; background: rgba(0,0,0,0.05); padding: 1px 4px; border-radius: 4px;">AZURE_TTS_KEY</code> vào <code>.env</code> để kích hoạt giọng đọc AI.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer Action Button --}}
            <div style="padding: 1rem 1.75rem; border-top: 1px solid #f1f5f9; background: #fafafa;">
                <button type="button" wire:click="mountAction('testTts')"
                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #8b5cf6; color: white; border: none; border-radius: 0.75rem; padding: 0.7rem 1rem; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" x2="12" y1="19" y2="22"></line>
                    </svg>
                    <span>Kiểm Tra Giọng Đọc (Test Audio)</span>
                </button>
            </div>
        </div>

        {{-- ─── Card 3: 🗄 Cache & Bộ nhớ ────────────────────────────────── --}}
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="height: 4px; background: linear-gradient(90deg, #f59e0b, #fbbf24); width: 100%;"></div>
            
            <div style="padding: 1.5rem 1.75rem;">
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="height: 2.75rem; width: 2.75rem; border-radius: 0.85rem; background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;">
                                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                <path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">Cache & Lưu trữ</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Trạng thái bộ nhớ đệm hệ thống</p>
                        </div>
                    </div>

                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; white-space: nowrap;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #d97706;"></span>
                        Hoạt động
                    </span>
                </div>

                {{-- Key-Value Details --}}
                <div style="display: flex; flex-direction: column; gap: 0.55rem; background: #f8fafc; border-radius: 0.85rem; padding: 1rem; border: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Trình điều khiển (Driver):</span>
                        <span style="font-family: monospace; font-weight: 700; color: #0f172a; background: white; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">{{ strtoupper($cacheDriver) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Bộ nhớ Cài đặt (Settings):</span>
                        <span style="font-weight: 700; color: {{ \Illuminate\Support\Facades\Cache::has('settings.all') ? '#16a34a' : '#64748b' }};">
                            {{ \Illuminate\Support\Facades\Cache::has('settings.all') ? '✓ Đang lưu cache' : '○ Sẽ nạp khi có truy cập' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Múi giờ ứng dụng:</span>
                        <span style="font-family: monospace; font-weight: 600; color: #0f172a;">{{ $timezone }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer Action Button --}}
            <div style="padding: 1rem 1.75rem; border-top: 1px solid #f1f5f9; background: #fafafa;">
                <button type="button" wire:click="mountAction('clearCache')"
                    style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #dc2626; color: white; border: none; border-radius: 0.75rem; padding: 0.7rem 1rem; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                    <span>Làm Mới Toàn Bộ Cache (Clear Cache)</span>
                </button>
            </div>
        </div>

        {{-- ─── Card 4: 🔧 Thông tin hệ thống ──────────────────────────── --}}
        <div style="background: white; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between;">
            <div style="height: 4px; background: linear-gradient(90deg, #10b981, #34d399); width: 100%;"></div>
            
            <div style="padding: 1.5rem 1.75rem;">
                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="height: 2.75rem; width: 2.75rem; border-radius: 0.85rem; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #10b981; flex-shrink: 0;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;">
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
                            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">Môi Trường & Phiên Bản</h3>
                            <p style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Cấu hình máy chủ hiện tại</p>
                        </div>
                    </div>

                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ app()->isProduction() ? '#dcfce7' : '#fef3c7' }}; color: {{ app()->isProduction() ? '#15803d' : '#92400e' }}; border: 1px solid {{ app()->isProduction() ? '#bbf7d0' : '#fde68a' }}; white-space: nowrap;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ app()->isProduction() ? '#16a34a' : '#d97706' }};"></span>
                        {{ strtoupper(app()->environment()) }}
                    </span>
                </div>

                {{-- Key-Value Details --}}
                <div style="display: flex; flex-direction: column; gap: 0.55rem; background: #f8fafc; border-radius: 0.85rem; padding: 1rem; border: 1px solid #f1f5f9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Phiên bản PHP:</span>
                        <span style="font-family: monospace; font-weight: 700; color: #0f172a; background: white; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0;">PHP {{ $phpVersion }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Laravel Framework:</span>
                        <span style="font-family: monospace; font-weight: 700; color: #991b1b; background: #fef2f2; padding: 2px 8px; border-radius: 6px; border: 1px solid #fee2e2;">Laravel {{ $laravelVersion }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                        <span style="color: #64748b; font-weight: 600;">Phiên bản Ứng dụng:</span>
                        <span style="font-family: monospace; font-weight: 700; color: #0f172a;">v{{ $appVersion }}</span>
                    </div>
                </div>
            </div>

            {{-- Footer Info Bar --}}
            <div style="padding: 1rem 1.75rem; border-top: 1px solid #f1f5f9; background: #fafafa; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.8rem; color: #15803d; font-weight: 700;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px;">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>Hệ thống hoạt động bình thường</span>
            </div>
        </div>

    </div>

    <x-filament-actions::modals />

</div>

</x-filament-panels::page>

