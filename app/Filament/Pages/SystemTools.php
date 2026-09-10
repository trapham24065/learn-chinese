<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class SystemTools extends Page
{
    protected string $view = 'filament.pages.system-tools';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;
    protected static ?string $navigationLabel = 'Công cụ hệ thống';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống';
    protected static ?int $navigationSort = 11;
    protected static ?string $title = 'Công cụ hệ thống';


    public function getViewData(): array
    {
        // SMTP Info (read from config, never expose password)
        $mailer       = config('mail.default', 'log');
        $smtpHost     = config('mail.mailers.smtp.host', '');
        $smtpPort     = config('mail.mailers.smtp.port', '');
        $smtpEnc      = config('mail.mailers.smtp.encryption', '');
        $fromAddress  = config('mail.from.address', '');
        $smtpConfigured = ($mailer === 'smtp' && !empty($smtpHost));

        // Azure TTS Info
        $ttsKey     = config('services.azure_tts.key', env('AZURE_TTS_KEY', ''));
        $ttsRegion  = config('services.azure_tts.region', env('AZURE_TTS_REGION', ''));
        $ttsConfigured = !empty($ttsKey) && !empty($ttsRegion);

        // System Info
        $appVersion = config('app.version', '1.0.0');
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $cacheDriver = config('cache.default', 'file');
        $timezone = config('app.timezone', 'UTC');

        return compact(
            'mailer', 'smtpHost', 'smtpPort', 'smtpEnc', 'fromAddress', 'smtpConfigured',
            'ttsKey', 'ttsRegion', 'ttsConfigured',
            'appVersion', 'phpVersion', 'laravelVersion', 'cacheDriver', 'timezone'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            // ─── Send Test Email ──────────────────────────────────────────
            Action::make('sendTestEmail')
                ->label('Gửi email test')
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('primary')
                ->form([
                    TextInput::make('email')
                        ->label('Email nhận')
                        ->email()
                        ->required()
                        ->placeholder('admin@example.com'),
                ])
                ->action(function (array $data): void {
                    try {
                        Mail::raw(
                            'Đây là email kiểm tra từ hệ thống Learn Chinese. Nếu bạn nhận được email này, cấu hình SMTP đang hoạt động tốt! 🎉',
                            function ($message) use ($data) {
                                $message->to($data['email'])
                                    ->subject('[Learn Chinese] Test Email - Kiểm tra SMTP');
                            }
                        );

                        Notification::make()
                            ->title('Email đã được gửi!')
                            ->body("Kiểm tra hộp thư của {$data['email']}.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gửi email thất bại!')
                            ->body('Lỗi: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // ─── Test TTS ────────────────────────────────────────────────
            Action::make('testTts')
                ->label('Test giọng đọc TTS')
                ->icon(Heroicon::OutlinedSpeakerWave)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Test Azure TTS')
                ->modalDescription('Sẽ gọi API Azure TTS để phát âm câu tiếng Trung. Kiểm tra log nếu không nghe thấy.')
                ->action(function (): void {
                    $key    = env('AZURE_TTS_KEY', '');
                    $region = env('AZURE_TTS_REGION', '');

                    if (empty($key) || empty($region)) {
                        Notification::make()
                            ->title('Chưa cấu hình Azure TTS')
                            ->body('Vui lòng thiết lập AZURE_TTS_KEY và AZURE_TTS_REGION trong file .env.')
                            ->warning()
                            ->send();
                        return;
                    }

                    try {
                        $tokenUrl = "https://{$region}.api.cognitive.microsoft.com/sts/v1.0/issueToken";
                        $client   = new \GuzzleHttp\Client(['timeout' => 5]);
                        $response = $client->post($tokenUrl, [
                            'headers' => ['Ocp-Apim-Subscription-Key' => $key],
                        ]);

                        if ($response->getStatusCode() === 200) {
                            Notification::make()
                                ->title('Azure TTS hoạt động!')
                                ->body("Kết nối tới region '{$region}' thành công. Token đã được cấp.")
                                ->success()
                                ->send();
                        } else {
                            throw new \Exception('HTTP ' . $response->getStatusCode());
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Kết nối Azure TTS thất bại!')
                            ->body('Lỗi: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // ─── Clear Cache ────────────────────────────────────────────
            Action::make('clearCache')
                ->label('Xóa cache')
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận xóa cache')
                ->modalDescription('Hành động này sẽ xóa toàn bộ application cache, view cache và settings cache.')
                ->action(function (): void {
                    try {
                        Cache::flush();
                        Artisan::call('view:clear');
                        Artisan::call('route:clear');

                        Notification::make()
                            ->title('Cache đã được xóa!')
                            ->body('Application cache, view cache và settings cache đã được làm mới.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Xóa cache thất bại!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
