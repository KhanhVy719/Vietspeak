# Hệ Thống Quản Lý & Đánh Giá Kỹ Năng Thuyết Trình

Ứng dụng web quản lý và đánh giá kỹ năng thuyết trình cho nhà trường, được xây dựng bằng Laravel 11.

## Tính Năng

### 🔐 3 Vai Trò Người Dùng

#### Admin

- Quản lý tài khoản giáo viên & học sinh (CRUD)
- Gán role cho người dùng
- Quản lý lớp học (CRUD)
- Thêm/xóa học sinh vào lớp
- Gán giáo viên phụ trách lớp
- Xem toàn bộ dữ liệu hệ thống

#### Giáo Viên

- Xem danh sách lớp được phân công
- Xem danh sách học sinh trong lớp
- Tạo/quản lý bài tập thuyết trình
- Xem bài nộp của học sinh
- Chấm điểm (0-10) và viết nhận xét
- Tải xuống file bài nộp
- Xem tổng hợp điểm của lớp

#### Học Sinh

- Xem thông tin cá nhân
- Xem các bài tập được giao
- Nộp bài (upload file PDF/PPTX/MP4, tối đa 200MB)
- Xem điểm và nhận xét từ giáo viên
- Chỉ xem được bài nộp của chính mình

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Database**: MySQL
- **Authentication**: Laravel Breeze (Blade + Tailwind CSS)
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Blade Templates, Tailwind CSS
- **File Storage**: Private Storage với kiểm tra quyền

## Cấu Trúc Thư Mục

```
presentation-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ClassroomController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Teacher/
│   │   │   │   ├── ClassroomController.php
│   │   │   │   ├── AssignmentController.php
│   │   │   │   ├── SubmissionController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Student/
│   │   │   │   ├── AssignmentController.php
│   │   │   │   ├── SubmissionController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── DownloadController.php
│   │   └── Requests/
│   │       ├── StoreAssignmentRequest.php
│   │       ├── StoreSubmissionRequest.php
│   │       ├── StoreUserRequest.php
│   │       └── StoreClassroomRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Classroom.php
│   │   ├── Assignment.php
│   │   ├── Submission.php
│   │   └── Grade.php
│   └── Policies/
│       ├── ClassroomPolicy.php
│       ├── AssignmentPolicy.php
│       ├── SubmissionPolicy.php
│       └── UserPolicy.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_classrooms_table.php
│   │   ├── 2024_01_01_000002_create_class_user_table.php
│   │   ├── 2024_01_01_000003_create_assignments_table.php
│   │   ├── 2024_01_01_000004_create_submissions_table.php
│   │   └── 2024_01_01_000005_create_grades_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
└── resources/
    └── views/
        ├── admin/
        ├── teacher/
        ├── student/
        ├── layouts/
        └── components/
```

## Cài Đặt & Chạy Project

### Yêu Cầu Hệ Thống

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0

### Bước 1: Clone hoặc tạo project

```bash
cd presentation-management
```

### Bước 2: Cài đặt dependencies

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies
npm install
```

### Bước 3: Cấu hình môi trường

```bash
# Copy file .env.example
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Bước 4: Cấu hình Database

Mở file `.env` và cấu hình database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=presentation_management
DB_USERNAME=root
DB_PASSWORD=
```

Tạo database:

```sql
CREATE DATABASE presentation_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 5: Chạy Migrations & Seeders

```bash
# Chạy migrations và seeders
php artisan migrate:fresh --seed
```

### Bước 6: Tạo symbolic link cho storage

```bash
php artisan storage:link
```

### Bước 7: Build assets

```bash
npm run build
```

### Bước 8: Chạy server

```bash
php artisan serve
```

Truy cập: `http://localhost:8000`

## Tài Khoản Demo

Sau khi chạy seeder, bạn có thể đăng nhập với các tài khoản sau:

### Admin

- Email: `admin@school.com`
- Password: `password`

### Giáo Viên

- Email: `teacher1@school.com` hoặc `teacher2@school.com`
- Password: `password`

### Học Sinh

- Email: `student1@school.com` đến `student10@school.com`
- Password: `password`

## Cấu Hình File Upload

Ứng dụng hỗ trợ upload file lên đến 200MB. Bạn có thể cần điều chỉnh file `php.ini`:

```ini
upload_max_filesize = 200M
post_max_size = 200M
max_execution_time = 300
```

## Cấu Trúc Database

### Bảng `users`

- Quản lý tất cả người dùng (Admin, Giáo viên, Học sinh)
- Sử dụng Spatie Permission để quản lý roles

### Bảng `classrooms`

- Lưu thông tin các lớp học

### Bảng `class_user` (Pivot)

- Liên kết người dùng với lớp học
- Phân biệt vai trò: teacher/student

### Bảng `assignments`

- Lưu các bài tập thuyết trình
- Liên kết với lớp học và giáo viên tạo

### Bảng `submissions`

- Lưu bài nộp của học sinh
- Chứa đường dẫn file và ghi chú

### Bảng `grades`

- Lưu điểm và nhận xét
- Liên kết với bài nộp

## Bảo Mật & Phân Quyền

- **Authentication**: Laravel Breeze
- **Authorization**: Policies kiểm tra quyền truy cập
- **File Storage**: Private storage, chỉ download được nếu có quyền
- **Validation**: Form Requests cho tất cả input

### Quy Tắc Phân Quyền

- **Admin**: Toàn quyền truy cập
- **Teacher**: Chỉ truy cập lớp được phân công
- **Student**: Chỉ xem bài tập và nộp bài của lớp mình

## Hướng Dẫn Sử Dụng

### Admin

1. Đăng nhập với tài khoản admin
2. Vào "Quản lý người dùng" để tạo tài khoản
3. Vào "Quản lý lớp học" để tạo lớp
4. Gán học sinh và giáo viên vào lớp

### Giáo Viên

1. Đăng nhập với tài khoản giáo viên
2. Xem danh sách lớp được phân công
3. Tạo bài tập cho lớp
4. Xem và chấm điểm bài nộp của học sinh

### Học Sinh

1. Đăng nhập với tài khoản học sinh
2. Xem danh sách bài tập
3. Upload file bài nộp
4. Xem điểm và nhận xét

## Lưu Ý Quan Trọng

- File upload được lưu trong `storage/app/private`
- Không thể truy cập trực tiếp, phải qua controller có kiểm tra quyền
- Hỗ trợ định dạng: PDF, PPTX, MP4
- Giới hạn kích thước: 200MB

## Troubleshooting

### Lỗi permission khi upload file

```bash
chmod -R 775 storage bootstrap/cache
```

### Lỗi class not found

```bash
composer dump-autoload
```

### Lỗi npm

```bash
rm -rf node_modules package-lock.json
npm install
```

## License

MIT License - Tự do sử dụng cho mục đích giáo dục.
