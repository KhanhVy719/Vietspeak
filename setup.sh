#!/bin/bash

# setup.sh - Auto Configure Domain & SSL for VietSpeak

echo "============================================="
echo "   VIETSPEAK VPS SETUP ASSISTANT 🚀"
echo "============================================="
echo ""

# 1. Ask for Domain
read -p "👉 Nhập tên miền của bạn (ví dụ: vietspeak.com): " DOMAIN_NAME
if [ -z "$DOMAIN_NAME" ]; then
  echo "❌ Tên miền không được để trống!"
  exit 1
fi

# 2. Ask for Email
read -p "👉 Nhập Email để đăng ký SSL (ví dụ: admin@gmail.com): " SSL_EMAIL
if [ -z "$SSL_EMAIL" ]; then
  echo "❌ Email không được để trống!"
  exit 1
fi

echo ""
echo "🔄 Đang cập nhật cấu hình cho Domain: $DOMAIN_NAME..."

# 3. Create .env file for Docker Compose to use
# We use an .env file so docker-compose can substitute variables easily
cat > .env.prod <<EOF
# Production Settings
DOMAIN_NAME=$DOMAIN_NAME
SSL_EMAIL=$SSL_EMAIL
EOF

echo "✅ Đã tạo file cấu hình môi trường (.env.prod)"

# 4. Update Laravel .env
echo "🔄 Đang cập nhật cấu hình Backend Laravel..."
LARAVEL_ENV="presentation-management/.env"

if [ -f "$LARAVEL_ENV" ]; then
  # Backup logic could be here, but user wants 'instant setup'
  # We use sed to replace lines. The delimiter is | to avoid conflicts with urls
  sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN_NAME|g" "$LARAVEL_ENV"
  sed -i "s|APP_ENV=.*|APP_ENV=production|g" "$LARAVEL_ENV"
  sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" "$LARAVEL_ENV"
  
  echo "✅ Đã cập nhật APP_URL, APP_ENV, APP_DEBUG trong Laravel."
else
  echo "⚠️ Không tìm thấy file $LARAVEL_ENV, bỏ qua bước này."
fi

# 5. Confirm and Run
echo ""
echo "============================================="
echo "   CẤU HÌNH HOÀN TẤT!"
echo "============================================="
echo "Tên miền: $DOMAIN_NAME"
echo "Email:    $SSL_EMAIL"
echo ""
read -p "❓ Bạn có muốn chạy server ngay bây giờ không? (y/n): " RUN_NOW

# Check for Docker Compose command
if docker compose version >/dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
    DOCKER_COMPOSE_CMD="docker-compose"
else
    echo "❌ Không tìm thấy Docker Compose! Vui lòng cài đặt trước."
    exit 1
fi

# Check if Docker Daemon is running
if ! docker info > /dev/null 2>&1; then
    echo "⚠️ Docker Daemon chưa chạy. Đang thử khởi động..."
    service docker start || systemctl start docker
    sleep 5
    
    if ! docker info > /dev/null 2>&1; then
        echo "❌ LỖI: Không thể kết nối với Docker Daemon."
        echo "👉 Hãy thử chạy lệnh: 'sudo service docker start' rồi chạy lại script này."
        exit 1
    fi
fi

if [ "$RUN_NOW" = "y" ] || [ "$RUN_NOW" = "Y" ]; then
  echo "🚀 Đang khởi động hệ thống..."
  
  # Run Docker Compose with error checking
  if ! $DOCKER_COMPOSE_CMD -f docker-compose.prod.yml --env-file .env.prod up -d --build; then
      echo "❌ Lỗi khi chạy Docker Compose. Vui lòng kiểm tra log ở trên."
      exit 1
  fi
  
  echo "⏳ Đang đợi Database và Server khởi động (15s)..."
  sleep 15

  echo "🛠️ Đang chạy các lệnh thiết lập cuối cùng..."
  docker exec laravel_app php artisan storage:link
  docker exec laravel_app php artisan migrate --force
  docker exec laravel_app php artisan config:cache
  docker exec laravel_app php artisan route:cache
  docker exec laravel_app php artisan view:cache
  
  echo ""
  echo "✅ Tối ưu hóa xong! Web đã sẵn sàng."
  echo "🎉 TRUY CẬP NGAY: https://$DOMAIN_NAME/vietspeak"
else
  echo ""
  echo "👉 Khi nào muốn chạy, hãy gõ lệnh:"
  echo "   $DOCKER_COMPOSE_CMD -f docker-compose.prod.yml --env-file .env.prod up -d --build"
fi
