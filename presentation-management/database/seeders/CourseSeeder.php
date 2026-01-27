<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Khóa Cơ Bản',
                'code' => 'BASIC-001',
                'description' => '10 buổi thực chiến: Làm chủ giọng nói, giải phóng hình thể và kiểm soát tâm lý sân khấu.',
                'instructor' => 'VietSpeak Team',
                'duration' => '10 buổi',
                'level' => 'beginner',
                'price' => 499000,
                'status' => 'active',
            ],
            [
                'name' => 'Khóa học nhóm',
                'code' => 'GROUP-001',
                'description' => '10 buổi: Nghệ thuật kể chuyện Storytelling, tư duy phản biện và xử lý khủng hoảng.',
                'instructor' => 'VietSpeak Team',
                'duration' => '10 buổi',
                'level' => 'intermediate',
                'price' => 799000,
                'status' => 'active',
            ],
            [
                'name' => 'Khóa 1-1 Cá Nhân',
                'code' => 'PLATINUM-001',
                'description' => 'Thiết kế riêng biệt theo mục tiêu cá nhân. Chỉnh sửa bài nói trực tiếp cùng Founder.',
                'instructor' => 'Founder',
                'duration' => '12 buổi',
                'level' => 'advanced',
                'price' => 1199000,
                'status' => 'active',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        $this->command->info('✅ Created 3 VietSpeak courses successfully!');
    }
}
