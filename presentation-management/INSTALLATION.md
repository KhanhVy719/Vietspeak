# Hướng Dẫn Cài Đặt Chi Tiết

# Laravel Presentation Management System

## Mục Lục

1. [Yêu Cầu Hệ Thống](#yêu-cầu-hệ-thống)
2. [Cài Đặt Ban Đầu](#cài-đặt-ban-đầu)
3. [Cài Đặt Laravel Breeze](#cài-đặt-laravel-breeze)
4. [Cấu Hình Database](#cấu-hình-database)
5. [Chạy Migrations và Seeders](#chạy-migrations-và-seeders)
6. [Cấu Hình File Upload](#cấu-hình-file-upload)
7. [Build Frontend Assets](#build-frontend-assets)
8. [Chạy Application](#chạy-application)
9. [Tài Khoản Demo](#tài-khoản-demo)
10. [Troubleshooting](#troubleshooting)

## Yêu Cầu Hệ Thống

- PHP >= 8.2
- Composer
- Node.js >= 18.x và NPM
- MySQL >= 8.0
- Git (optional)

### Kiểm Tra Phiên Bản

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

## Cài Đặt Ban Đầu

### Bước 1: Di chuyển vào thư mục project

```bash
cd d:\DuAn\DuAnGiKhongBiet\presentation-management
```

### Bước 2: Cài đặt PHP Dependencies

```bash
composer install
```

Nếu gặp lỗi, thử:

```bash
composer install --ignore-platform-reqs
```

### Bước 3: Cài đặt Node.js Dependencies

```bash
npm install
```

### Bước 4: Copy file environment

```bash
copy .env.example .env
```

### Bước 5: Generate Application Key

```bash
php artisan key:generate
```

## Cài Đặt Laravel Breeze

Laravel Breeze đã được thêm vào composer.json. Bạn cần publish các file authentication:

```bash
php artisan breeze:install blade
```

Chọn các option sau khi được hỏi:

- Which Breeze stack would you like to install? **blade**
- Would you like dark mode support? **no**
- Which testing framework do you prefer? **PHPUnit**

Sau đó chạy:

```bash
npm install
npm run build
```

## Cấu Hình Database

### Tạo Database

Mở MySQL command line hoặc phpMyAdmin và chạy:

```sql
CREATE DATABASE presentation_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Cấu Hình File .env

Mở file `.env` và cập nhật thông tin database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presentation_management
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

**Lưu ý:** Thay `your_password_here` bằng password MySQL của bạn.

### Cấu Hình Timezone

Đảm bảo timezone trong `.env` là:

```env
APP_TIMEZONE=Asia/Ho_Chi_Minh
```

## Chạy Migrations và Seeders

### Bước 1: Publish Spatie Permission Migrations

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Bước 2: Chạy Migrations

```bash
php artisan migrate:fresh
```

**Lưu ý:** Lệnh này sẽ xóa toàn bộ dữ liệu cũ (nếu có). Nếu bạn muốn giữ dữ liệu, dùng `php artisan migrate` thay vì `migrate:fresh`.

### Bước 3: Chạy Seeders

```bash
php artisan db:seed
```

Hoặc chạy cả hai lệnh cùng lúc:

```bash
php artisan migrate:fresh --seed
```

Sau khi chạy xong, bạn sẽ thấy thông báo:

```
==============================================
Database seeded successfully!
==============================================
Demo accounts:
- Admin: admin@school.com / password
- Teacher 1: teacher1@school.com / password
- Teacher 2: teacher2@school.com / password
- Students: student1@school.com to student10@school.com / password
==============================================
```

## Cấu Hình File Upload

### Tạo Symbolic Link

```bash
php artisan storage:link
```

### Tạo Thư Mục Private Storage

```bash
mkdir storage\app\private
mkdir storage\app\private\submissions
```

### Cấu Hình PHP.ini (Cho Upload File Lớn)

Mở file `php.ini` và cập nhật:

```ini
upload_max_filesize = 200M
post_max_size = 200M
max_execution_time = 300
memory_limit = 512M
```

Để tìm file `php.ini`:

```bash
php --ini
```

Restart web server sau khi thay đổi.

## Build Frontend Assets

### Development Mode (với Hot Reload)

```bash
npm run dev
```

Giữ terminal này chạy khi đang develop. Mở terminal mới để chạy các lệnh khác.

### Production Mode

```bash
npm run build
```

## Chạy Application

Mở terminal mới (nếu đang chạy `npm run dev`) và chạy:

```bash
php artisan serve
```

Application sẽ chạy tại: **http://localhost:8000**

## Tài Khoản Demo

Sau khi seed database, bạn có thể đăng nhập với các tài khoản sau:

### Admin

```
Email: admin@school.com
Password: password
```

### Giáo Viên

```
Email: teacher1@school.com hoặc teacher2@school.com
Password: password
```

### Học Sinh

```
Email: student1@school.com đến student10@school.com
Password: password
```

## Cấu Trúc Dữ Liệu Demo

Sau khi seed:

- **2 Lớp học:**
  - Lớp 10A1: có teacher1 và 5 học sinh (student1-student5)
  - Lớp 11B2: có teacher1, teacher2 và 5 học sinh (student6-student10)

- **4 Bài tập:**
  - 2 bài cho Lớp 10A1
  - 2 bài cho Lớp 11B2

## Workflow Test Sau Khi Cài Đặt

### 1. Test Admin

1. Đăng nhập với `admin@school.com`
2. Vào "Quản lý người dùng" → Tạo user mới
3. Vào "Quản lý lớp học" → Tạo lớp mới
4. Thêm học sinh và giáo viên vào lớp

### 2. Test Teacher

1. Đăng nhập với `teacher1@school.com`
2. Xem danh sách lớp được phân công
3. Vào một lớp → Tạo bài tập mới
4. Đợi học sinh nộp bài (hoặc test bằng tài khoản student)
5. Chấm điểm bài nộp

### 3. Test Student

1. Đăng nhập với `student1@school.com`
2. Xem danh sách bài tập
3. Click vào bài tập → Nộp bài
4. Upload file (PDF/PPTX/MP4) + ghi chú
5. Xem điểm sau khi giáo viên chấm

## Troubleshooting

### Lỗi: Class not found

```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Lỗi: Permission denied (Storage)

Windows:

```bash
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### Lỗi: npm packages

```bash
rm -rf node_modules package-lock.json
npm install
```

### Lỗi: Vite manifest not found

```bash
npm run build
```

### Lỗi: SQLSTATE Connection refused

- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin trong file `.env`
- Thử kết nối MySQL bằng MySQL Workbench hoặc command line

### Lỗi: File upload quá lớn

Kiểm tra:

1. File `php.ini` đã cấu hình đúng chưa
2. Restart web server sau khi thay đổi `php.ini`
3. Nếu dùng XAMPP/WAMP, restart Apache

### Lỗi: 419 Page Expired

- Clear browser cache
- Chạy: `php artisan config:clear`

## Các Lệnh Hữu Ích

```bash
# Clear all cache
php artisan optimize:clear

# Cache config
php artisan config:cache
php artisan route:cache

# View routes
php artisan route:list

# Tạo controller mới
php artisan make:controller ControllerName

# Tạo model mới
php artisan make:model ModelName -m

# Xem logs
tail -f storage/logs/laravel.log  # Linux/Mac
Get-Content storage\logs\laravel.log -Tail 50 -Wait  # Windows PowerShell
```

## Lưu Ý Quan Trọng

1. **File Upload:** File được lưu trong `storage/app/private`, không thể truy cập trực tiếp qua browser
2. **Download:** Phải qua route `/downloads/submissions/{id}` có kiểm tra quyền
3. **Authorization:** Tất cả routes đều có Policy kiểm tra quyền truy cập
4. **Password Demo:** Tất cả tài khoản demo đều dùng password `password`

## Bảo Mật Khi Deploy Production

Khi deploy lên production, nhớ:

1. Thay đổi `APP_KEY`
2. Set `APP_DEBUG=false`
3. Set `APP_ENV=production`
4. Thay đổi password của tất cả users
5. Xóa hoặc comment code trong DatabaseSeeder
6. Cấu hình HTTPS
7. Cấu hình firewall
8. Backup database định kỳ

## Hỗ Trợ

Nếu gặp vấn đề, kiểm tra:

- File `storage/logs/laravel.log` để xem lỗi chi tiết
- Browser Console (F12) để xem lỗi JavaScript
- Network tab trong Browser DevTools để xem request/response

---

**Chúc bạn cài đặt thành công!** 🎉
