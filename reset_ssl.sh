#!/bin/bash

echo "⚠️  CẢNH BÁO: Script này sẽ XÓA toàn bộ SSL cũ và xin cấp lại từ đầu."
echo "👉 Dùng khi gặp lỗi SSL Handshake Failed hoặc lỗi chứng chỉ."
echo ""
read -p "Bạn có chắc chắn muốn tiếp tục? (y/n): " confirm
if [[ "$confirm" != "y" ]]; then
    exit 1
fi

echo "🛑 Đang dừng các container liên quan..."
docker stop nginx-proxy nginx-proxy-acme web_server laravel_app
docker rm nginx-proxy nginx-proxy-acme web_server laravel_app

echo "🧹 Đang xóa các volume chứa chứng chỉ cũ (để xin mới)..."
docker volume rm acme certs vhost html dhparam 2>/dev/null

echo "🔄 Đang chạy lại cấu hình..."
# Đảm bảo setup.sh đã có config đúng
if [ ! -f ".env.prod" ]; then
    echo "❌ Không tìm thấy file cấu hình .env.prod! Hãy chạy ./setup.sh trước."
    exit 1
fi

echo "🚀 Khởi động lại hệ thống..."
# FIX: Explicitly load .env.prod to avoid "variable is not set" warnings
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d --force-recreate

echo "✅ Hoàn tất! Quá trình xin SSL mới sẽ mất khoảng 1-2 phút."
echo "👉 Hãy kiểm tra logs xem có lỗi gì không: docker logs -f nginx-proxy-acme"
