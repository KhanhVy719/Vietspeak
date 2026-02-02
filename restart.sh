#!/bin/bash

# restart.sh - Quick Update & Restart for VietSpeak

echo "🚀 Bắt đầu quá trình cập nhật & khởi động lại..."

# 1. Pull code mới nhất
echo "📥 Đang tải code mới về (git pull)..."
git pull

# 2. Restart Docker Containers
echo "🔄 Đang khởi động lại Docker Containers..."
# Tùy chọn: Dùng docker compose restart nếu config không đổi
# docker compose -f docker-compose.prod.yml restart 

# Tốt nhất: Dùng up -d --build để đảm bảo config mới và build lại nếu cần
if [ -f "docker-compose.prod.yml" ]; then
    docker compose -f docker-compose.prod.yml up -d --build
else
    # Fallback nếu user chưa setup prod
    docker compose up -d --build
fi

# 3. Clear Cache Laravel (Optional but recommended)
echo "🧹 Đang dọn dẹp cache..."
docker exec laravel_app php artisan config:clear
docker exec laravel_app php artisan route:clear
docker exec laravel_app php artisan view:clear

echo "✅ Hoàn tất! Website đã được cập nhật."
