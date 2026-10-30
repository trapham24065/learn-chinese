#!/usr/bin/env bash
# ==============================================================================
# SCRIPT THIẾT LẬP MÔI TRƯỜNG LẦN ĐẦU TRÊN ARMBIAN LINUX (AMLOGIC S905W)
# ==============================================================================

set -e

# Tự động nhận diện thư mục dự án dù viết hoa hay thường (/var/www/learn-chinese)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

echo "==> Bắt đầu thiết lập Learn-Chinese tại: $PROJECT_DIR"

echo "==> [1/7] Cập nhật danh sách gói hệ thống..."
sudo apt update -y

echo "==> [2/7] Cài đặt Nginx, PHP 8.2-FPM, MariaDB và các gói cần thiết..."
sudo apt install -y nginx mariadb-server curl git unzip \
    php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
    php8.2-zip php8.2-gd php8.2-bcmath php8.2-opcache

echo "==> [3/7] Cài đặt Composer toàn cục..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> [4/7] Sao chép và liên kết cấu hình Nginx..."
sudo sed "s|root /var/www/Learn-Chinese/public;|root $PROJECT_DIR/public;|g" \
    "$PROJECT_DIR/deploy/nginx/learn-chinese.conf" | sudo tee /etc/nginx/sites-available/learn-chinese.conf > /dev/null

sudo ln -sf /etc/nginx/sites-available/learn-chinese.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

echo "==> [5/7] Áp dụng cấu hình PHP-FPM và MariaDB..."
sudo cp "$PROJECT_DIR/deploy/php/armbian-php-fpm.conf" /etc/php/8.2/fpm/pool.d/www.conf
sudo cp "$PROJECT_DIR/deploy/php/opcache-armbian.ini" /etc/php/8.2/mods-available/opcache.ini
sudo cp "$PROJECT_DIR/deploy/mysql/armbian-mariadb.cnf" /etc/mysql/mariadb.conf.d/99-armbian-tuned.cnf

# Systemd Timer cho Chinese Duel sync
sudo sed "s|/var/www/Learn-Chinese|$PROJECT_DIR|g" \
    "$PROJECT_DIR/deploy/systemd/duel-sync.service" | sudo tee /etc/systemd/system/duel-sync.service > /dev/null

sudo cp "$PROJECT_DIR/deploy/systemd/duel-sync.timer" /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now duel-sync.timer

echo "==> [6/7] Khởi động lại các dịch vụ hệ thống..."
sudo systemctl restart mariadb
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "==> [7/7] Thiết lập Crontab Laravel Scheduler & Phân quyền..."
(sudo crontab -u www-data -l 2>/dev/null | grep -F "artisan schedule:run") || (
    (sudo crontab -u www-data -l 2>/dev/null; echo "* * * * * php $PROJECT_DIR/artisan schedule:run >> /dev/null 2>&1") | sudo crontab -u www-data -
)

sudo chown -R www-data:www-data "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
sudo chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"

echo "=============================================================================="
echo "THIẾT LẬP MÔI TRƯỜNG ARMBIAN THÀNH CÔNG!"
echo "Đường dẫn: $PROJECT_DIR"
echo "Bây giờ bạn có thể chạy: bash deploy/scripts/deploy-armbian.sh"
echo "=============================================================================="
