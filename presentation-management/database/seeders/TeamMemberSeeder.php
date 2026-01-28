<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMember;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Hoàng Việt Anh',
                'initials' => 'HA',
                'title' => 'CO-FOUNDER & BỘ PHẬN KỸ THUẬT AI',
                'description' => 'Phụ trách định hướng và phát triển các giải pháp AI cốt lõi của dự án, tập trung vào phân tích, tối ưu và ứng dụng trí tuệ nhân tạo trong đào tạo kỹ năng thuyết trình và giao tiếp.',
                'avatar_color' => '#1a3a5f',
                'order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Hoàng Thị Trang Nhi',
                'initials' => 'HN',
                'title' => 'CO-FOUNDER VÀ BỘ PHẬN THUYẾT TRÌNH',
                'description' => 'Đảm nhiệm vai trò cố vấn chuyên môn về thuyết trình, tâm lý học hành vi và đào tạo kỹ năng sẵn sàng, giúp người học xây dựng sự tự tin, kiểm soát cảm xúc và truyền đạt hiệu quả thông điệp trước đám đông.',
                'avatar_color' => '#e63946',
                'order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Trần Nguyễn Hoàng Khang',
                'initials' => 'HK',
                'title' => 'CO-FOUNDER & BỘ PHẬN CSDL',
                'description' => 'Chịu trách nhiệm thiết kế, quản lý và tối ưu hệ thống cơ sở dữ liệu, đảm bảo tính ổn định, bảo mật và khả năng mở rộng của dữ liệu, hỗ trợ cho nền tảng phân tích và học tập trực tuyến của dự án.',
                'avatar_color' => '#f4a624',
                'order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($members as $member) {
            TeamMember::create($member);
        }
    }
}
