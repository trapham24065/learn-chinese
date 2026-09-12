<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel = 'Cài đặt chung';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Cài đặt chung';


    // Form state — bound to each setting key
    public ?array $data = [];

    public function mount(): void
    {
        $svc = app(SettingsService::class);

        $this->form->fill([
            // Website
            'site_name'            => $svc->get('site_name', 'Learn Chinese'),
            'site_slogan'          => $svc->get('site_slogan', ''),
            'contact_email'        => $svc->get('contact_email', ''),
            'allow_registration'   => (bool) $svc->get('allow_registration', '1'),
            'maintenance_mode'     => (bool) $svc->get('maintenance_mode', '0'),
            'maintenance_message'  => $svc->get('maintenance_message', ''),

            // Announcement
            'announcement_enabled'     => (bool) $svc->get('announcement_enabled', '0'),
            'announcement_type'        => $svc->get('announcement_type', 'info'),
            'announcement_title'       => $svc->get('announcement_title', ''),
            'announcement_content'     => $svc->get('announcement_content', ''),
            'announcement_audience'    => $svc->get('announcement_audience', 'all'),
            'announcement_start'       => $svc->get('announcement_start'),
            'announcement_end'         => $svc->get('announcement_end'),
            'announcement_dismissible' => (bool) $svc->get('announcement_dismissible', '1'),

            // Learning
            'flashcard_daily_limit' => (int) $svc->get('flashcard_daily_limit', 50),
            'streak_reset_hour'     => (int) $svc->get('streak_reset_hour', 0),
            'default_daily_goal'    => (int) $svc->get('default_daily_goal', 20),

            // Features
            'feature_flashcards'  => (bool) $svc->get('feature_flashcards', '1'),
            'feature_quiz'        => (bool) $svc->get('feature_quiz', '1'),
            'feature_dictionary'  => (bool) $svc->get('feature_dictionary', '1'),
            'feature_stories'     => (bool) $svc->get('feature_stories', '1'),
            'feature_hsk_mock'    => (bool) $svc->get('feature_hsk_mock', '1'),
            'feature_courses'     => (bool) $svc->get('feature_courses', '1'),

            // Bank & Courses
            'bank_id'              => $svc->get('bank_id', 'MB'),
            'bank_account'         => $svc->get('bank_account', '0988888888'),
            'bank_account_name'    => $svc->get('bank_account_name', 'TIENG TRUNG CO GIAO'),
            'course_consult_phone' => $svc->get('course_consult_phone', '0988888888'),
            'course_consult_zalo'  => $svc->get('course_consult_zalo', '0988888888'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([

                // ─── 🌐 Website ────────────────────────────────────────────
                Section::make('🌐 Thông tin Website')
                    ->description('Thông tin cơ bản và cài đặt truy cập website.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Tên website')
                            ->helperText('Tên hiển thị trên toàn bộ giao diện.')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('site_slogan')
                            ->label('Slogan')
                            ->helperText('Câu mô tả ngắn gọn về website.')
                            ->maxLength(200),

                        TextInput::make('contact_email')
                            ->label('Email liên hệ')
                            ->helperText('Email hiển thị để học viên liên hệ hỗ trợ.')
                            ->email()
                            ->maxLength(100),

                        Toggle::make('allow_registration')
                            ->label('Cho phép đăng ký mới')
                            ->helperText('Nếu tắt, trang /register sẽ hiển thị thông báo đóng đăng ký.')
                            ->inline(false),

                        Toggle::make('maintenance_mode')
                            ->label('Chế độ bảo trì')
                            ->helperText('Admin vẫn truy cập được. Học viên sẽ thấy trang bảo trì.')
                            ->inline(false),

                        Textarea::make('maintenance_message')
                            ->label('Thông báo bảo trì')
                            ->helperText('Nội dung hiển thị trên trang bảo trì khi đang bật.')
                            ->rows(2)
                            ->maxLength(500),
                    ])->columns(2),

                // ─── 📢 Announcement ────────────────────────────────────────
                Section::make('📢 Thông báo & Banner')
                    ->description('Hiển thị banner thông báo ở đầu trang học viên.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('announcement_enabled')
                            ->label('Bật banner thông báo')
                            ->inline(false)
                            ->columnSpanFull(),

                        Select::make('announcement_type')
                            ->label('Loại thông báo')
                            ->options([
                                'info'    => '🔵 Thông tin (Info)',
                                'success' => '🟢 Thành công (Success)',
                                'warning' => '🟡 Cảnh báo (Warning)',
                                'error'   => '🔴 Khẩn cấp (Error)',
                            ])
                            ->native(false),

                        Select::make('announcement_audience')
                            ->label('Đối tượng hiển thị')
                            ->options([
                                'all'           => 'Tất cả người dùng',
                                'guests'        => 'Chỉ khách (chưa đăng nhập)',
                                'authenticated' => 'Chỉ học viên đã đăng nhập',
                            ])
                            ->native(false),

                        TextInput::make('announcement_title')
                            ->label('Tiêu đề')
                            ->maxLength(150)
                            ->placeholder('Ví dụ: Thông báo nâng cấp hệ thống'),

                        Textarea::make('announcement_content')
                            ->label('Nội dung thông báo')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->placeholder('Nội dung chi tiết của banner...'),

                        DateTimePicker::make('announcement_start')
                            ->label('Thời gian bắt đầu')
                            ->helperText('Để trống nếu muốn hiển thị ngay.')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i'),

                        DateTimePicker::make('announcement_end')
                            ->label('Thời gian kết thúc')
                            ->helperText('Để trống nếu hiển thị vô thời hạn.')
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i'),

                        Toggle::make('announcement_dismissible')
                            ->label('Cho phép đóng banner')
                            ->helperText('Học viên bấm × để đóng. Trạng thái lưu vào localStorage.')
                            ->inline(false),
                    ])->columns(2),

                // ─── 🎓 Learning ────────────────────────────────────────────
                Section::make('🎓 Cài đặt Học tập')
                    ->description('Giới hạn và mục tiêu học tập mặc định.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('flashcard_daily_limit')
                            ->label('Giới hạn flashcard mỗi ngày')
                            ->helperText('Số flashcard tối đa mỗi ngày. 0 = không giới hạn.')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(9999)
                            ->suffix('thẻ'),

                        TextInput::make('default_daily_goal')
                            ->label('Mục tiêu học mỗi ngày')
                            ->helperText('Số từ/bài mặc định hiển thị trong dashboard học viên.')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->suffix('từ/bài'),

                        TextInput::make('streak_reset_hour')
                            ->label('Giờ kiểm tra streak (0–23)')
                            ->helperText('Giờ trong ngày hệ thống tính lại streak. Thường là 0 (nửa đêm).')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(23)
                            ->suffix('giờ'),
                    ])->columns(3),

                // ─── 🚀 Feature Flags ────────────────────────────────────────
                Section::make('🚀 Quản lý Tính năng')
                    ->description('Bật/tắt từng tính năng. Tắt sẽ ẩn khỏi menu điều hướng.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('feature_flashcards')
                            ->label('Thẻ ghi nhớ (Flashcards)')
                            ->inline(false),

                        Toggle::make('feature_quiz')
                            ->label('Luyện tập nhanh (Quiz)')
                            ->inline(false),

                        Toggle::make('feature_dictionary')
                            ->label('Từ điển & Video (YouGlish)')
                            ->inline(false),

                        Toggle::make('feature_stories')
                            ->label('Luyện đọc hiểu (Stories)')
                            ->inline(false),

                        Toggle::make('feature_hsk_mock')
                            ->label('Thi thử HSK')
                            ->inline(false),

                        Toggle::make('feature_courses')
                            ->label('Khóa học & Lớp Online (Meet)')
                            ->inline(false),
                    ])->columns(3),

                // ─── 💳 VietQR & Bank Settings ──────────────────────────────
                Section::make('💳 Tài khoản Ngân hàng (VietQR Học phí)')
                    ->description('Cấu hình thông tin tài khoản ngân hàng để tạo mã thanh toán VietQR tự động cho học viên.')
                    ->collapsible()
                    ->schema([
                        Select::make('bank_id')
                            ->label('Ngân hàng')
                            ->options([
                                'MB' => 'MBBank (Ngân hàng Quân đội)',
                                'VCB' => 'Vietcombank (Ngân hàng Ngoại thương)',
                                'TCB' => 'Techcombank (Ngân hàng Kỹ thương)',
                                'ICB' => 'VietinBank (Ngân hàng Công thương)',
                                'BIDV' => 'BIDV (Đầu tư & Phát triển)',
                                'ACB' => 'ACB (Á Châu)',
                                'VPB' => 'VPBank (Việt Nam Thịnh Vượng)',
                                'TPB' => 'TPBank (Tiên Phong)',
                                'MSB' => 'MSB (Hàng Hải)',
                                'OCB' => 'OCB (Phương Đông)',
                                'VIB' => 'VIB (Quốc Tế)',
                            ])
                            ->default('MB')
                            ->required(),

                        TextInput::make('bank_account')
                            ->label('Số tài khoản nhận tiền')
                            ->required()
                            ->maxLength(30),

                        TextInput::make('bank_account_name')
                            ->label('Tên chủ tài khoản (Không dấu)')
                            ->helperText('Ví dụ: NGUYEN THI HUONG')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('course_consult_phone')
                            ->label('Hotline tư vấn tuyển sinh')
                            ->helperText('Số điện thoại hiển thị trên các trang khóa học')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('course_consult_zalo')
                            ->label('SĐT / Link Zalo hỗ trợ')
                            ->helperText('Học viên có thể bấm vào để chat trực tiếp')
                            ->maxLength(100),
                    ])->columns(2),

            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $svc  = app(SettingsService::class);

        $svc->setMany($data);

        Notification::make()
            ->title('Đã lưu cài đặt!')
            ->body('Tất cả thay đổi đã được lưu và cache đã được làm mới.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
