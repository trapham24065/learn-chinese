<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-mail {email : Địa chỉ email nhận thử nghiệm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email thử nghiệm và chuẩn đoán kết nối cấu hình SMTP';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->argument('email');

        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Địa chỉ email '{$recipient}' không hợp lệ!");
            return Command::FAILURE;
        }

        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $scheme = config('mail.mailers.smtp.scheme') ?: 'none';
        $from = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->info("=== THÔNG TIN CẤU HÌNH MAIL ===");
        $this->line("• Driver (MAIL_MAILER): <comment>{$mailer}</comment>");
        $this->line("• Host: <comment>{$host}:{$port}</comment> (Scheme: {$scheme})");
        $this->line("• Người gửi (From): <comment>{$fromName} <{$from}></comment>");
        $this->line("• Người nhận: <comment>{$recipient}</comment>");
        $this->newLine();

        if ($mailer === 'log') {
            $this->warn("⚠️  CẢNH BÁO: MAIL_MAILER hiện đang đặt là 'log'.");
            $this->line("→ Laravel sẽ KHÔNG gửi email thật ra internet (không tới hộp thư Gmail/Outlook).");
            $this->line("→ Nội dung email sẽ chỉ được ghi vào file: storage/logs/laravel.log");
            $this->line("→ Để gửi email thật, hãy đổi MAIL_MAILER=smtp và cấu hình SMTP trong file .env.");
            $this->newLine();
        }

        $this->info("Đang tiến hành gửi email thử nghiệm...");

        try {
            Mail::raw("Xin chào!\n\nĐây là email kiểm tra hệ thống gửi thư từ website Learn Chinese.\nNếu bạn nhận được email này, cấu hình gửi thư SMTP của bạn đã hoạt động hoàn hảo!\n\nThời gian: " . now()->format('d/m/Y H:i:s'), function ($message) use ($recipient, $from, $fromName) {
                $message->to($recipient)
                        ->from($from, $fromName)
                        ->subject('[Learn Chinese] Email kiểm tra kết nối hệ thống');
            });

            if ($mailer === 'log') {
                $this->info("✓ Đã ghi email vào storage/logs/laravel.log thành công (Chế độ log).");
            } else {
                $this->info("✓ THÀNH CÔNG: Đã gửi email thử nghiệm tới <{$recipient}>!");
                $this->line("Hãy kiểm tra Hộp thư đến (Inbox) hoặc Thư rác (Spam) của bạn.");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error("❌ GỬI EMAIL THẤT BẠI!");
            $this->error("Chi tiết lỗi: " . $e->getMessage());
            $this->newLine();

            $this->warn("=== HƯỚNG DẪN KHẮC PHỤC ===");
            if (str_contains($e->getMessage(), 'Connection could not be established')) {
                $this->line("1. Máy chủ không kết nối được tới host/port SMTP.");
                $this->line("   - Nếu dùng Gmail: MAIL_HOST=smtp.gmail.com, MAIL_PORT=587, MAIL_ENCRYPTION=tls");
                $this->line("   - Kiểm tra xem firewall VPS có chặn cổng 587/465 không.");
            } elseif (str_contains($e->getMessage(), '535') || str_contains(strtolower($e->getMessage()), 'auth')) {
                $this->line("1. Lỗi xác thực tài khoản (Username hoặc Mật khẩu không đúng).");
                $this->line("   - Nếu dùng Gmail, KHÔNG dùng mật khẩu đăng nhập tài khoản thông thường.");
                $this->line("   - Phải bật 2-Step Verification và tạo 'Mật khẩu ứng dụng' (App Password) 16 ký tự.");
            } else {
                $this->line("1. Kiểm tra lại thông tin cấu hình trong file .env và chạy: php artisan config:clear");
            }

            return Command::FAILURE;
        }
    }
}
