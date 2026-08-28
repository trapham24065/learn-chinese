<x-filament-panels::page>

@php
    $hanNums = ['一','二','三','四','五','六'];
    $maxMinutes = max(1, max($chart['minutes'] ?? [1]));
@endphp

<div class="space-y-6">

    {{-- ══ 1. HERO BANNER & QUICK ACTIONS ══ --}}
    <div style="
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 45%, #7f1d1d 85%, #991b1b 100%);
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
        ">汉</div>

        <div style="position: relative; z-index: 1; display: flex; flex-direction: column; gap: 1.5rem; justify-content: space-between;" class="lg:flex-row lg:items-center">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(251, 191, 36, 0.15); border: 1px solid rgba(251, 191, 36, 0.35); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; color: #fde68a;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span>Trung tâm Quản trị Học tập · Learn Chinese LMS</span>
                </div>

                <h1 style="font-size: 1.85rem; font-weight: 900; letter-spacing: -0.02em; margin-top: 0.75rem; line-height: 1.2;">
                    Xin chào Quản trị viên 👋
                </h1>
                
                <p style="margin-top: 0.5rem; font-size: 0.875rem; color: rgba(255, 255, 255, 0.8); max-width: 38rem; line-height: 1.6;">
                    Theo dõi hoạt động học viên thời gian thực, quản lý kho học liệu HSK 1 - 6, kết quả thi thử mô phỏng và các chỉ số tăng trưởng của hệ thống.
                </p>
            </div>

            {{-- Quick Action Buttons --}}
            <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; align-items: center;">
                <a href="{{ $urls['create_flashcard'] }}"
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.1rem; border-radius: 0.875rem; font-size: 0.8rem; font-weight: 700; background: #fbbf24; color: #0f172a; text-decoration: none; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3); transition: all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Thêm Từ Vựng</span>
                </a>

                <a href="{{ $urls['create_question'] }}"
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.1rem; border-radius: 0.875rem; font-size: 0.8rem; font-weight: 700; background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.25); text-decoration: none; backdrop-filter: blur(8px); transition: all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>Thêm Đề Thi HSK</span>
                </a>

                <a href="{{ $urls['manage_hsk'] }}"
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.1rem; border-radius: 0.875rem; font-size: 0.8rem; font-weight: 700; background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.25); text-decoration: none; backdrop-filter: blur(8px); transition: all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    <span>Quản lý HSK 1-6</span>
                </a>

                <a href="{{ url('/') }}" target="_blank"
                   style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 0.9rem; border-radius: 0.875rem; font-size: 0.8rem; font-weight: 700; background: rgba(0, 0, 0, 0.3); color: #cbd5e1; text-decoration: none; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.2s;"
                   title="Mở giao diện website học viên">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <span>Xem Web</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ══ 2. FOUR BESPOKE KPI METRIC CARDS ══ --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        
        {{-- Card 1: Học Viên --}}
        <div style="background: white; border-radius: 1.25rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b;">Tổng học viên</span>
                <div style="height: 2.5rem; width: 2.5rem; border-radius: 0.75rem; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
            </div>
            <div style="margin-top: 0.75rem; display: flex; align-items: baseline; gap: 0.6rem;">
                <span style="font-size: 2.25rem; font-weight: 900; color: #0f172a; line-height: 1;">{{ number_format($kpis['total_students']) }}</span>
                @if($kpis['new_students_7d'] > 0)
                <span style="font-size: 0.75rem; font-weight: 700; color: #16a34a; background: #dcfce7; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                    +{{ $kpis['new_students_7d'] }} tuần này
                </span>
                @endif
            </div>
            <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span>Hoạt động 7 ngày:</span>
                <strong style="color: #0f172a;">{{ $kpis['active_students_7d'] }} học viên</strong>
            </div>
        </div>

        {{-- Card 2: Kho Học Liệu --}}
        <div style="background: white; border-radius: 1.25rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b;">Kho học liệu</span>
                <div style="height: 2.5rem; width: 2.5rem; border-radius: 0.75rem; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
            </div>
            <div style="margin-top: 0.75rem; display: flex; align-items: baseline; gap: 0.6rem;">
                <span style="font-size: 2.25rem; font-weight: 900; color: #0f172a; line-height: 1;">{{ number_format($kpis['total_flashcards']) }}</span>
                <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">từ vựng</span>
            </div>
            <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span>Bài học & Đề thi:</span>
                <strong style="color: #0f172a;">{{ $kpis['total_lessons'] }} bài · {{ $kpis['total_questions'] }} câu</strong>
            </div>
        </div>

        {{-- Card 3: Thời Lượng Học --}}
        <div style="background: white; border-radius: 1.25rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b;">Thời lượng học</span>
                <div style="height: 2.5rem; width: 2.5rem; border-radius: 0.75rem; background: #f0fdf4; display: flex; align-items: center; justify-content: center; color: #16a34a;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
            <div style="margin-top: 0.75rem; display: flex; align-items: baseline; gap: 0.6rem;">
                <span style="font-size: 2.25rem; font-weight: 900; color: #0f172a; line-height: 1;">{{ $kpis['total_study_hours'] }}</span>
                <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">giờ tích lũy</span>
            </div>
            <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span>Điểm TB / Buổi học 7d:</span>
                <strong style="color: #0f172a;">{{ $kpis['avg_score'] }}% · {{ $kpis['total_sessions_7d'] }} buổi</strong>
            </div>
        </div>

        {{-- Card 4: Thi Thử HSK --}}
        <div style="background: white; border-radius: 1.25rem; padding: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); position: relative; overflow: hidden;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b;">Thi thử HSK</span>
                <div style="height: 2.5rem; width: 2.5rem; border-radius: 0.75rem; background: #fdf2f8; display: flex; align-items: center; justify-content: center; color: #db2777;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                </div>
            </div>
            <div style="margin-top: 0.75rem; display: flex; align-items: baseline; gap: 0.6rem;">
                <span style="font-size: 2.25rem; font-weight: 900; color: #0f172a; line-height: 1;">{{ number_format($kpis['total_mock_tests']) }}</span>
                <span style="font-size: 0.75rem; font-weight: 700; color: #991b1b; background: #fee2e2; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                    Đỗ: {{ $kpis['mock_pass_rate'] }}%
                </span>
            </div>
            <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600;">
                <span>Chứng nhận đã cấp:</span>
                <strong style="color: #991b1b;">{{ $kpis['total_certificates'] }} chứng chỉ</strong>
            </div>
        </div>

    </div>

    {{-- ══ 3. MAIN SECTION: 2 COLUMNS ══ --}}
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;" class="xl:grid-cols-[1.6fr_1fr]">

        {{-- LEFT COLUMN: Charts & Recent Mock Tests --}}
        <div class="space-y-6">

            {{-- 14-Day Study Activity Visual Bar Chart --}}
            <div style="background: white; border-radius: 1.25rem; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="height: 0.6rem; width: 0.6rem; border-radius: 9999px; background: #991b1b;"></span>
                            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Hoạt động tự học 14 ngày qua</h2>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Tổng số phút học tập & lượt học mỗi ngày</p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; font-weight: 700;">
                        <span style="display: flex; align-items: center; gap: 0.35rem; color: #991b1b;">
                            <span style="height: 8px; width: 8px; border-radius: 2px; background: #991b1b;"></span> Phút học
                        </span>
                        <span style="display: flex; align-items: center; gap: 0.35rem; color: #64748b;">
                            <span style="height: 8px; width: 8px; border-radius: 2px; background: #e2e8f0;"></span> Buổi học
                        </span>
                    </div>
                </div>

                {{-- Visual Bar Chart Bars --}}
                <div style="display: grid; grid-template-columns: repeat(14, 1fr); gap: 0.5rem; align-items: flex-end; height: 160px; padding-top: 1rem; border-bottom: 1px solid #f1f5f9;">
                    @foreach($chart['labels'] as $idx => $date)
                        @php
                            $mins = $chart['minutes'][$idx] ?? 0;
                            $sessions = $chart['sessions'][$idx] ?? 0;
                            $heightPercent = max(8, min(100, round(($mins / $maxMinutes) * 100)));
                        @endphp
                        <div style="display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;" title="{{ $date }}: {{ $mins }} phút ({{ $sessions }} lượt)">
                            {{-- Value on top --}}
                            <span style="font-size: 0.6rem; font-weight: 800; color: {{ $mins > 0 ? '#991b1b' : '#cbd5e1' }}; margin-bottom: 4px;">
                                {{ $mins > 0 ? $mins : '0' }}
                            </span>
                            
                            {{-- Bar --}}
                            <div style="
                                width: 100%;
                                max-width: 28px;
                                height: {{ $heightPercent }}%;
                                border-radius: 6px 6px 2px 2px;
                                background: {{ $mins > 0 ? 'linear-gradient(180deg, #991b1b 0%, #b91c1c 100%)' : '#f1f5f9' }};
                                transition: all 0.2s;
                            "></div>
                            
                            {{-- Date label --}}
                            <span style="font-size: 0.65rem; font-weight: 600; color: #64748b; margin-top: 8px; white-space: nowrap;">
                                {{ $date }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Latest Mock Test Submissions Table --}}
            <div style="background: white; border-radius: 1.25rem; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="height: 0.6rem; width: 0.6rem; border-radius: 9999px; background: #d97706;"></span>
                            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Bài thi thử HSK vừa nộp</h2>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Kết quả thi và cấp chứng nhận mới nhất</p>
                    </div>

                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; background: #f8fafc; padding: 0.35rem 0.75rem; border-radius: 0.6rem; border: 1px solid #e2e8f0;">
                        {{ $kpis['total_mock_tests'] }} lượt đã nộp
                    </span>
                </div>

                @if($recentMockTests->isEmpty())
                    <div style="text-align: center; padding: 2.5rem 1rem; color: #94a3b8; font-size: 0.85rem;">
                        Chưa có học viên nào nộp bài thi thử HSK.
                    </div>
                @else
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; text-align: left; font-size: 0.8rem; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1.5px solid #f1f5f9; color: #64748b; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <th style="padding: 0.75rem 0.5rem;">Học viên</th>
                                    <th style="padding: 0.75rem 0.5rem;">Cấp độ</th>
                                    <th style="padding: 0.75rem 0.5rem;">Điểm Nghe/Đọc/NP</th>
                                    <th style="padding: 0.75rem 0.5rem;">Tổng / 300</th>
                                    <th style="padding: 0.75rem 0.5rem;">Trạng thái</th>
                                    <th style="padding: 0.75rem 0.5rem; text-align: right;">Chứng nhận</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMockTests as $test)
                                <tr style="border-bottom: 1px solid #f8fafc; transition: background 0.15s;">
                                    <td style="padding: 0.75rem 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="height: 1.85rem; width: 1.85rem; border-radius: 0.5rem; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800;">
                                                {{ strtoupper(substr($test->user->name ?? 'K', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0f172a;">{{ $test->user->name ?? 'Khách vãng lai' }}</div>
                                                <div style="font-size: 0.65rem; color: #94a3b8;">{{ $test->completed_at?->diffForHumans() ?? 'Gần đây' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem;">
                                        <span style="font-weight: 800; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 0.4rem; background: #fee2e2; color: #991b1b;">
                                            HSK {{ $test->hsk_level }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem; font-size: 0.75rem; color: #64748b;">
                                        <span>{{ $test->listening_score }}</span> /
                                        <span>{{ $test->reading_score }}</span> /
                                        <span>{{ $test->grammar_score }}</span>
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem; font-weight: 900; font-size: 0.9rem; color: {{ $test->passed ? '#16a34a' : '#dc2626' }};">
                                        {{ $test->total_score }}
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem;">
                                        @if($test->passed)
                                            <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.55rem; border-radius: 9999px; background: #dcfce7; color: #15803d;">
                                                ✓ ĐẠT ({{ $test->grade_text }})
                                            </span>
                                        @else
                                            <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.55rem; border-radius: 9999px; background: #fee2e2; color: #b91c1c;">
                                                ✕ CHƯA ĐẠT
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                        @if($test->certificate_code)
                                            <a href="{{ route('hsk.mock.certificate', $test->certificate_code) }}" target="_blank"
                                               style="font-size: 0.7rem; font-weight: 700; color: #991b1b; text-decoration: underline;">
                                                {{ $test->certificate_code }} ↗
                                            </a>
                                        @else
                                            <span style="font-size: 0.7rem; color: #cbd5e1;">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN: HSK 1 - 6 Matrix & Top Students --}}
        <div class="space-y-6">

            {{-- HSK 1 - 6 Content & Performance Matrix --}}
            <div style="background: white; border-radius: 1.25rem; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="height: 0.6rem; width: 0.6rem; border-radius: 9999px; background: #2563eb;"></span>
                            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Kho học liệu HSK 1 - 6</h2>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Phân bổ từ vựng, câu hỏi & tỷ lệ đỗ</p>
                    </div>

                    <a href="{{ $urls['manage_hsk'] }}" style="font-size: 0.75rem; font-weight: 700; color: #2563eb; text-decoration: none;">
                        Chi tiết ↗
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($hskMatrix as $row)
                    <div style="padding: 0.85rem; border-radius: 0.875rem; background: #f8fafc; border: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="height: 2.25rem; width: 2.25rem; border-radius: 0.6rem; background: {{ $row['color'] }}; color: white; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 900; line-height: 1;">
                                <span style="font-size: 0.6rem; opacity: 0.8;">HSK</span>
                                <span style="font-size: 0.85rem;">{{ $row['level'] }}</span>
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 800; color: #0f172a;">
                                    {{ $row['flashcards'] }} từ · {{ $row['lessons'] }} bài học
                                </div>
                                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.15rem;">
                                    Đề thi: <strong>{{ $row['questions'] }} câu</strong> (🎧{{ $row['listening_q'] }} · 📖{{ $row['reading_q'] }} · ✍️{{ $row['grammar_q'] }})
                                </div>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div style="font-size: 0.85rem; font-weight: 900; color: {{ $row['pass_rate'] >= 60 ? '#16a34a' : '#d97706' }};">
                                {{ $row['pass_rate'] }}%
                            </div>
                            <div style="font-size: 0.65rem; color: #94a3b8;">
                                {{ $row['mock_tests_count'] }} lượt thi
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Top Active / Streak Students --}}
            <div style="background: white; border-radius: 1.25rem; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="height: 0.6rem; width: 0.6rem; border-radius: 9999px; background: #f59e0b;"></span>
                            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a;">Học viên tích cực nhất</h2>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">Chuỗi Streak & thời gian học cao nhất</p>
                    </div>

                    <a href="{{ $urls['students'] }}" style="font-size: 0.75rem; font-weight: 700; color: #f59e0b; text-decoration: none;">
                        Xem tất cả ↗
                    </a>
                </div>

                @if($topStudents->isEmpty())
                    <div style="text-align: center; padding: 2rem 1rem; color: #94a3b8; font-size: 0.85rem;">
                        Chưa có dữ liệu học viên.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topStudents as $rank => $st)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.5rem; border-bottom: 1px solid #f8fafc;">
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <span style="font-size: 0.75rem; font-weight: 900; color: {{ $rank === 0 ? '#f59e0b' : ($rank === 1 ? '#94a3b8' : '#cbd5e1') }}; width: 1.2rem;">
                                    #{{ $rank + 1 }}
                                </span>
                                <div style="height: 2rem; width: 2rem; border-radius: 0.6rem; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">
                                    {{ strtoupper(substr($st->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size: 0.8rem; font-weight: 700; color: #0f172a;">{{ $st->name }}</div>
                                    <div style="font-size: 0.7rem; color: #64748b;">
                                        {{ $st->completed_lessons }} bài xong · {{ round(($st->total_minutes ?? 0) / 60, 1) }}h học
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; font-weight: 800; color: #ea580c; background: #fff7ed; padding: 0.25rem 0.6rem; border-radius: 9999px;">
                                <span>🔥</span>
                                <span>{{ $st->streak ?? 0 }} ngày</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

</x-filament-panels::page>
