# 🚀 Hướng dẫn Triển khai lên VPS (Có Tên Miền + SSL Tự Động)

## 1. Chuẩn bị Môi trường (Cho VPS mới tinh - Ubuntu)

Copy và chạy toàn bộ lệnh sau để cài Docker & Git:

```bash
# Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y

# Cài Git và Curl
sudo apt install -y git curl

# Cài Docker tự động
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Cài Docker Compose (Nếu chưa có)
sudo apt install -y docker-compose-plugin
```

## 2. Cài đặt Nhanh (Khuyên dùng)

Tại thư mục dự án trên VPS, chỉ cần chạy file setup:

```bash
chmod +x setup.sh
./setup.sh
```

**Script sẽ tự động hỏi:**

- Tên miền của bạn là gì?
- Email của bạn là gì?
- Và tự động cấu hình SSL + Chạy server luôn.

Bạn không cần sửa file thủ công nữa! 🎉

```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

**Hệ thống sẽ tự động:**

- Tải về, Build code.
- Cài đặt SSL miễn phí (Let's Encrypt).
- Chạy web server.

Truy cập: `https://your-domain.com/vietspeak` để trải nghiệm!
