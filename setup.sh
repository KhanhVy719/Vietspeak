#!/bin/bash

# setup.sh - Auto Configure Domain & SSL for VietSpeak (Dual Domain Support)

echo "============================================="
echo "   VIETSPEAK VPS SETUP ASSISTANT 🚀"
echo "============================================="
echo ""

# 1. Ask for Domains
echo "👉 CẤU HÌNH TÊN MIỀN RIÊNG BIỆT:"
echo "---------------------------------"

# 1.1 Backend Domain (Admin + API)
read -p "1. Nhập tên miền cho ADMIN/API (VD: api.vietspeak.com): " BACKEND_DOMAIN
if [ -z "$BACKEND_DOMAIN" ]; then
  echo "❌ Tên miền Admin không được để trống!"
  exit 1
fi

# 1.2 Frontend Domain (Student Portal)
read -p "2. Nhập tên miền cho HỌC VIÊN (VD: vietspeak.com): " FRONTEND_DOMAIN
if [ -z "$FRONTEND_DOMAIN" ]; then
  echo "❌ Tên miền Học viên không được để trống!"
  exit 1
fi

# 1.3 Check & Install Docker (Auto)
if ! command -v docker &> /dev/null; then
    echo ""
    echo "📦 KHÔNG TÌM THẤY DOCKER! ĐANG TỰ ĐỘNG CÀI ĐẶT..."
    echo "PLEASE WAIT / VUI LÒNG ĐỢI..."
    
    # Update & Install Curl if missing
    if [ -x "$(command -v apt-get)" ]; then
        apt-get update >/dev/null 2>&1
        apt-get install -y curl git >/dev/null 2>&1
    elif [ -x "$(command -v yum)" ]; then
        yum install -y curl git >/dev/null 2>&1
    fi

    # Install Docker using official script
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
    
    # Enable service
    service docker start 2>/dev/null || systemctl start docker 2>/dev/null
    
    echo "✅ Đã cài đặt xong Docker!"
    echo ""
fi

# 2. Ask for Email
read -p "👉 Nhập Email để đăng ký SSL (ví dụ: admin@gmail.com): " SSL_EMAIL
if [ -z "$SSL_EMAIL" ]; then
  echo "❌ Email không được để trống!"
  exit 1
fi

echo ""
echo "🔄 Đang cập nhật cấu hình cho 2 Domain:"
echo "   - Backend: $BACKEND_DOMAIN"
echo "   - Frontend: $FRONTEND_DOMAIN"

# 3. Create .env file for Docker Compose to use
# We use a comma-separated list for VIRTUAL_HOST to support multiple domains
cat > .env.prod <<EOF
# Production Settings
DOMAINS=$BACKEND_DOMAIN,$FRONTEND_DOMAIN
SSL_EMAIL=$SSL_EMAIL
EOF

echo "✅ Đã tạo file cấu hình môi trường (.env.prod)"

# 3.1 Generate Nginx Config Dynamically
echo "🔄 Đang tạo cấu hình Nginx (docker/nginx/default.prod.conf)..."
cat > docker/nginx/default.prod.conf <<EOF
# SERVER 1: BACKEND (Laravel Admin + API)
server {
    listen 80;
    server_name $BACKEND_DOMAIN;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
        gzip_static on;
    }

    location ~ \.php$ {
        try_files \$uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
    }
}

# SERVER 2: FRONTEND (VietSpeak Student Portal)
server {
    listen 80;
    server_name $FRONTEND_DOMAIN;
    root /var/www/vietspeak;
    index index.html;

    location / {
        try_files $uri $uri.html $uri/ =404;
    }

    # Optional: Redirect /api calls to backend if needed (but we use CORS now)
    # location /api {
    #    return 301 https://$BACKEND_DOMAIN\$request_uri;
    # }
}
EOF
echo "✅ Đã tạo cấu hình Nginx riêng biệt cho 2 tên miền."

# 3.2 Update Frontend Config (config.js) to point to Backend Domain
echo "🔄 Đang cập nhật kết nối Frontend -> Backend..."
cat > VietSpeak/config.js <<EOF
const CONFIG = {
    API_URL: 'https://$BACKEND_DOMAIN/api',
    DEBUG: false
};
EOF
echo "✅ Đã cập nhật VietSpeak/config.js"


# 4. Update Laravel .env
echo "🔄 Đang cập nhật cấu hình Backend Laravel..."
LARAVEL_ENV="presentation-management/.env"

if [ -f "$LARAVEL_ENV" ]; then
  # Use | delimiter for sed to handle URLs
  sed -i "s|APP_URL=.*|APP_URL=https://$BACKEND_DOMAIN|g" "$LARAVEL_ENV"
  sed -i "s|APP_ENV=.*|APP_ENV=production|g" "$LARAVEL_ENV"
  sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|g" "$LARAVEL_ENV"
  
  # Update Database Connection (Docker Internal Network)
  sed -i "s|DB_HOST=.*|DB_HOST=db|g" "$LARAVEL_ENV"
  sed -i "s|DB_PORT=.*|DB_PORT=5432|g" "$LARAVEL_ENV"
  sed -i "s|DB_DATABASE=.*|DB_DATABASE=presentation_management|g" "$LARAVEL_ENV"
  sed -i "s|DB_USERNAME=.*|DB_USERNAME=postgres|g" "$LARAVEL_ENV"
  sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=password|g" "$LARAVEL_ENV"

  echo "✅ Đã cập nhật APP_URL thành: https://$BACKEND_DOMAIN"
else
  echo "⚠️ Không tìm thấy file $LARAVEL_ENV, bỏ qua bước này."
fi

# 5. Confirm and Run
echo ""
echo "============================================="
echo "   CẤU HÌNH HOÀN TẤT!"
echo "============================================="
echo "Frontend (Student): https://$FRONTEND_DOMAIN"
echo "Backend (Admin):    https://$BACKEND_DOMAIN"
echo "SSL Email:          $SSL_EMAIL"
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
  
  # Fix missing vendor folder due to volume mount
  echo "📦 Đang cài đặt Dependencies (Vendor)..."
  docker exec laravel_app composer install --no-interaction --optimize-autoloader

  # Ensure storage directories exist and are writable
  echo "🔧 Đang sửa quyền thư mục (Permissions)..."
  docker exec laravel_app bash -c "mkdir -p storage/framework/{sessions,views,cache} storage/logs"
  docker exec laravel_app chmod -R 777 storage bootstrap/cache

  docker exec laravel_app php artisan storage:link
  docker exec laravel_app php artisan migrate --force
  docker exec laravel_app php artisan config:cache
  docker exec laravel_app php artisan route:cache
  
  # Only run view:cache if config is loaded
  docker exec laravel_app php artisan view:cache || echo "⚠️ Không thể cache view, nhưng web vẫn sẽ chạy ổn."
  
  echo ""
  echo "✅ Tối ưu hóa xong! Web đã sẵn sàng."
  echo "🎉 TRUY CẬP HỌC VIÊN: https://$FRONTEND_DOMAIN"
  echo "🔧 TRUY CẬP ADMIN:    https://$BACKEND_DOMAIN/login"
else
  echo ""
  echo "👉 Khi nào muốn chạy, hãy gõ lệnh:"
  echo "   $DOCKER_COMPOSE_CMD -f docker-compose.prod.yml --env-file .env.prod up -d --build"
fi
