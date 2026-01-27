# 🚀 Hướng dẫn Triển khai lên VPS (Có Tên Miền + SSL Tự Động)

## 1. Chuẩn bị

- Một VPS (Ubuntu/CentOS) đã cài **Docker** và **Docker Compose**.
- Một tên miền (ví dụ: `vietspeak.com`) đã trỏ về IP của VPS.

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
