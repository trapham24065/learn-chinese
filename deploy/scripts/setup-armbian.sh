#!/usr/bin/env bash
# ==============================================================================
# SCRIPT THIẾT LẬP MÔI TRƯỜNG LẦN ĐẦU TRÊN ARMBIAN LINUX (AMLOGIC S905W)
# ==============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

echo "==> Bắt đầu thiết lập Learn-Chinese tại: $PROJECT_DIR"

# Kiểm tra nếu máy đã có sẵn Nginx và PHP
if command -v nginx &> /dev/null && command -v php &> /dev/null; then
    echo "==> [Thông báo] Nginx và PHP đã được cài đặt sẵn trên hệ thống."
    echo "==> Bỏ qua bước apt update/install để tiết kiệm thời gian và tránh nghẽn mạng."
else
    echo "==> [1/7] Cập nhật danh sách gói hệ thống (bỏ qua tệp Contents dung lượng lớn)..."
    apt-get update -o Acquire::IndexTargets::deb::Contents-deb=false -y || true

    echo "==> [2/7] Cài đặt Nginx, PHP, MariaDB và các gói cần thiết..."
    apt-get install -y --no-install-recommends nginx mariadb-server curl git unzip \
        php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
        php8.2-zip php8.2-gd php8.2-bcmath php8.2-opcache || \
    apt-get install -y --no-install-recommends nginx mariadb-server curl git unzip php-fpm php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd php-bcmath php-opcache
fi

echo "==> [3/7] Kiểm tra Composer..."
if ! command -v composer &> /dev/null && [ ! -f /usr/local/bin/composer ]; then
    echo "==> Cài đặt Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

echo "==> [4/7] Sao chép cấu hình Nginx..."
if [ -f "$PROJECT_DIR/deploy/nginx/learn-chinese.conf" ]; then
    sed "s|root /var/www/Learn-Chinese/public;|root $PROJECT_DIR/public;|g" \
        "$PROJECT_DIR/deploy/nginx/learn-chinese.conf" > /etc/nginx/sites-available/learn-chinese.conf
    ln -sf /etc/nginx/sites-available/learn-chinese.conf /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
fi

echo "==> [5/7] Áp dụng cấu hình PHP-FPM, MariaDB và Systemd Timer..."
# PHP-FPM & OPcache
PHP_POOL_DIR=$(ls -d /etc/php/*/fpm/pool.d 2>/dev/null | head -n 1 || echo "")
if [ -n "$PHP_POOL_DIR" ] && [ -f "$PROJECT_DIR/deploy/php/armbian-php-fpm.conf" ]; then
    cp "$PROJECT_DIR/deploy/php/armbian-php-fpm.conf" "$PHP_POOL_DIR/www.conf"
fi

PHP_MODS_DIR=$(ls -d /etc/php/*/mods-available 2>/dev/null | head -n 1 || echo "")
if [ -n "$PHP_MODS_DIR" ] && [ -f "$PROJECT_DIR/deploy/php/opcache-armbian.ini" ]; then
    cp "$PROJECT_DIR/deploy/php/opcache-armbian.ini" "$PHP_MODS_DIR/opcache.ini"
fi

# MariaDB config
if [ -d /etc/mysql/mariadb.conf.d ] && [ -f "$PROJECT_DIR/deploy/mysql/armbian-mariadb.cnf" ]; then
    cp "$PROJECT_DIR/deploy/mysql/armbian-mariadb.cnf" /etc/mysql/mariadb.conf.d/99-armbian-tuned.cnf
fi

# Systemd Timer cho Chinese Duel sync
if [ -f "$PROJECT_DIR/deploy/systemd/duel-sync.service" ]; then
    sed "s|/var/www/Learn-Chinese|$PROJECT_DIR|g" \
        "$PROJECT_DIR/deploy/systemd/duel-sync.service" > /etc/systemd/system/duel-sync.service
    cp "$PROJECT_DIR/deploy/systemd/duel-sync.timer" /etc/systemd/system/
    systemctl daemon-reload
    systemctl enable --now duel-sync.timer
fi

echo "==> [6/7] Khởi động lại các dịch vụ hệ thống..."
systemctl restart mariadb 2>/dev/null || systemctl restart mysql 2>/dev/null || true
PHP_FPM_SVC=$(systemctl list-unit-files | grep -oE 'php[0-9.]*-fpm\.service' | head -n 1 || echo "")
if [ -n "$PHP_FPM_SVC" ]; then
    systemctl restart "$PHP_FPM_SVC" || true
fi
systemctl restart nginx 2>/dev/null || true

echo "==> [7/7] Thiết lập Crontab Laravel Scheduler & Phân quyền..."
(crontab -u www-data -l 2>/dev/null | grep -F "artisan schedule:run") || (
    (crontab -u www-data -l 2>/dev/null; echo "* * * * * php $PROJECT_DIR/artisan schedule:run >> /dev/null 2>&1") | crontab -u www-data -
)

chown -R www-data:www-data "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"

echo "=============================================================================="
echo "THIẾT LẬP MÔI TRƯỜNG ARMBIAN THÀNH CÔNG!"
echo "Bây giờ bạn có thể chạy: bash deploy/scripts/deploy-armbian.sh"
echo "=============================================================================="
