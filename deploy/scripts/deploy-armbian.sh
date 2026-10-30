#!/usr/bin/env bash
# ==============================================================================
# SCRIPT DEPLOY LEARN-CHINESE TRÊN ARMBIAN LINUX (AMLOGIC S905W)
# ==============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"
cd "$PROJECT_DIR"

echo "==> Bắt đầu cập nhật và deploy tại: $PROJECT_DIR"

echo "==> [1/6] Kéo cập nhật mới nhất từ Git repository..."
git pull origin main

echo "==> [2/6] Cài đặt dependencies (Composer no-dev)..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "==> [3/6] Chạy Database Migrations..."
php artisan migrate --force

echo "==> [4/6] Tối ưu hóa Cache (Config, Route, View)..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> [5/6] Thiết lập quyền sở hữu và phân quyền thư mục..."
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo "==> [6/6] Reload PHP-FPM và Nginx để nạp OPcache mới..."
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

# Đảm bảo duel-sync timer đang chạy
if systemctl is-active --quiet duel-sync.timer; then
    echo "==> Duel Sync Timer: ACTIVE"
else
    echo "==> Khởi động Duel Sync Timer..."
    sudo systemctl enable --now duel-sync.timer
fi

echo "=============================================================================="
echo "DEPLOY THÀNH CÔNG TRÊN ARMBIAN LINUX!"
echo "=============================================================================="
