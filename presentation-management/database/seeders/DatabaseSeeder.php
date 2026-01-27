<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo roles
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);

        // Tạo 1 Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Tạo 2 Giáo viên
        $teacher1 = User::create([
            'name' => 'Nguyễn Văn A',
            'email' => 'teacher1@school.com',
            'password' => Hash::make('password'),
        ]);
        $teacher1->assignRole('teacher');

        $teacher2 = User::create([
            'name' => 'Trần Thị B',
            'email' => 'teacher2@school.com',
            'password' => Hash::make('password'),
        ]);
        $teacher2->assignRole('teacher');

        // Tạo 10 Học sinh
        $students = [];
        for ($i = 1; $i <= 10; $i++) {
            $student = User::create([
                'name' => "Học sinh $i",
                'email' => "student$i@school.com",
                'password' => Hash::make('password'),
            ]);
            $student->assignRole('student');
            $students[] = $student;
        }

        // Tạo 2 Lớp học
        $class1 = Classroom::create([
            'name' => 'Lớp 10A1',
            'description' => 'Lớp học kỹ năng thuyết trình nâng cao',
        ]);

        $class2 = Classroom::create([
            'name' => 'Lớp 11B2',
            'description' => 'Lớp học kỹ năng thuyết trình cơ bản',
        ]);

        // Gán giáo viên vào lớp
        // Lớp 1: teacher1
        $class1->teachers()->attach($teacher1->id, ['type' => 'teacher']);
        
        // Lớp 2: teacher1 và teacher2
        $class2->teachers()->attach($teacher1->id, ['type' => 'teacher']);
        $class2->teachers()->attach($teacher2->id, ['type' => 'teacher']);

        // Gán học sinh vào lớp
        // Lớp 1: 5 học sinh đầu
        for ($i = 0; $i < 5; $i++) {
            $class1->students()->attach($students[$i]->id, ['type' => 'student']);
        }

        // Lớp 2: 5 học sinh sau
        for ($i = 5; $i < 10; $i++) {
            $class2->students()->attach($students[$i]->id, ['type' => 'student']);
        }

        // Tạo bài tập mẫu
        $assignment1 = Assignment::create([
            'classroom_id' => $class1->id,
            'title' => 'Thuyết trình về chủ đề Bảo vệ môi trường',
            'description' => 'Chuẩn bị bài thuyết trình 10-15 phút về các giải pháp bảo vệ môi trường. Yêu cầu có slide PowerPoint và video demo.',
            'due_date' => now()->addDays(7),
            'created_by' => $teacher1->id,
        ]);

        $assignment2 = Assignment::create([
            'classroom_id' => $class1->id,
            'title' => 'Thuyết trình về công nghệ AI',
            'description' => 'Giới thiệu về ứng dụng của AI trong cuộc sống hàng ngày.',
            'due_date' => now()->addDays(14),
            'created_by' => $teacher1->id,
        ]);

        $assignment3 = Assignment::create([
            'classroom_id' => $class2->id,
            'title' => 'Giới thiệu bản thân',
            'description' => 'Thuyết trình giới thiệu bản thân trong 5 phút.',
            'due_date' => now()->addDays(3),
            'created_by' => $teacher2->id,
        ]);

        $assignment4 = Assignment::create([
            'classroom_id' => $class2->id,
            'title' => 'Thuyết trình về nghề nghiệp tương lai',
            'description' => 'Chia sẻ về nghề nghiệp mà bạn mong muốn theo đuổi trong tương lai.',
            'due_date' => now()->addDays(10),
            'created_by' => $teacher1->id,
        ]);

        $this->command->info('==============================================');
        $this->command->info('Database seeded successfully!');
        $this->command->info('==============================================');
        $this->command->info('Demo accounts:');
        $this->command->info('- Admin: admin@school.com / password');
        $this->command->info('- Teacher 1: teacher1@school.com / password');
        $this->command->info('- Teacher 2: teacher2@school.com / password');
        $this->command->info('- Students: student1@school.com to student10@school.com / password');
        $this->command->info('==============================================');
    }
}
