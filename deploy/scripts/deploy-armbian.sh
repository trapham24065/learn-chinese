#!/usr/bin/env bash
# ==============================================================================
# SCRIPT DEPLOY LEARN-CHINESE TRÊN ARMBIAN LINUX (AMLOGIC S905W)
# ==============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
cd "$PROJECT_DIR"

echo "==> Bắt đầu cập nhật và deploy tại: $PROJECT_DIR"

# Cho phép chạy composer dưới quyền root trên Armbian
export COMPOSER_ALLOW_SUPERUSER=1

echo "==> [1/6] Kéo cập nhật mới nhất từ Git repository..."
git pull origin main

echo "==> [2/6] Cài đặt dependencies (Composer no-dev)..."
if command -v composer &> /dev/null; then
    composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
elif [ -f /usr/local/bin/composer ]; then
    /usr/local/bin/composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
else
    echo "Composer không tìm thấy, bỏ qua bước composer install."
fi

echo "==> [3/6] Chạy Database Migrations cho Chinese Duel..."
php artisan migrate --force

echo "==> [4/6] Tối ưu hóa Cache (Config, Route, View)..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> [5/6] Thiết lập quyền sở hữu và phân quyền thư mục..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> [6/6] Reload PHP-FPM và Nginx..."
# Tự động tìm dịch vụ PHP-FPM đang chạy
PHP_FPM_SVC=$(systemctl list-units --type=service --state=running | grep -oE 'php[0-9.]*-fpm\.service' | head -n 1 || echo "")
if [ -n "$PHP_FPM_SVC" ]; then
    echo "Reloading $PHP_FPM_SVC..."
    systemctl reload "$PHP_FPM_SVC" || systemctl restart "$PHP_FPM_SVC" || true
else
    systemctl reload php8.2-fpm 2>/dev/null || systemctl reload php-fpm 2>/dev/null || true
fi

systemctl reload nginx 2>/dev/null || systemctl restart nginx 2>/dev/null || true

# Tự động cài đặt & kích hoạt Systemd Duel Sync Timer nếu chưa có
if [ -f "$PROJECT_DIR/deploy/systemd/duel-sync.service" ]; then
    if [ ! -f /etc/systemd/system/duel-sync.service ]; then
        echo "==> Đang cài đặt Systemd Duel Sync Timer (15s)..."
        sed "s|/var/www/Learn-Chinese|$PROJECT_DIR|g" "$PROJECT_DIR/deploy/systemd/duel-sync.service" > /etc/systemd/system/duel-sync.service
        cp "$PROJECT_DIR/deploy/systemd/duel-sync.timer" /etc/systemd/system/
        systemctl daemon-reload
        systemctl enable --now duel-sync.timer
        echo "==> Duel Sync Timer đã được kích hoạt thành công!"
    else
        systemctl enable --now duel-sync.timer 2>/dev/null || true
    fi
fi

echo "=============================================================================="
echo "DEPLOY THÀNH CÔNG TRÊN ARMBIAN LINUX!"
echo "=============================================================================="
