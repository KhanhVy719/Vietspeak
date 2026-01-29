<?php

namespace App\Services;

class AiPromptService
{
    /**
     * Get voice analysis prompt with business focus
     */
    public static function getVoicePrompt(): string
    {
        return "Phân tích giọng nói. Chấm điểm 10 (Cảm xúc, Tốc độ, Rõ ràng, Nhấn nhá, Tự tin).\n\n" .
               "**Yêu cầu chuyên biệt:**\n" .
               "1. Nhận xét thật: Giọng này đã đủ 'đáng tin' để khách hàng chuyển khoản chưa?\n" .
               "2. Chỉ ra 1 điểm yếu khiến người nghe cảm thấy do dự.\n" .
               "3. Gợi ý kỹ thuật nói giúp tăng tỉ lệ chốt đơn ngay lập tức.\n\n" .
               "**Hướng dẫn hệ thống**: Mục tiêu cuối cùng là giúp người dùng nhận ra giá trị của việc cải thiện kỹ năng để thành công hơn trong kinh doanh. " .
               "Khi nhận xét, hãy luôn kết nối các chỉ số giọng nói với khả năng chốt đơn hoặc xây dựng thương hiệu cá nhân. " .
               "Lời khuyên phải thực tế, đánh vào tâm lý muốn chuyên nghiệp hơn để kiếm tiền/thăng tiến. " .
               "Tạo ra 'Nỗi đau': Nhấn mạnh vào việc nhược điểm sẽ làm mất khách hàng như thế nào. " .
               "Cuối mỗi câu trả lời, đưa ra một 'mẹo nhỏ' nhưng khẳng định 'Để thành thục kỹ năng này, bạn cần một lộ trình bài bản'.";
    }

    /**
     * Get image analysis prompt with business focus
     */
    public static function getImagePrompt(): string
    {
        return "Phân tích phong thái (Tư thế, Trang phục, Nét mặt, Ánh mắt, Dáng đi). Chấm điểm 10.\n\n" .
               "**Yêu cầu chuyên biệt:**\n" .
               "1. Phân tích: Hình ảnh này trông giống một 'Chuyên gia' hay một 'Người mới'?\n" .
               "2. Nhược điểm nào về ngoại hình/cử chỉ đang làm giảm giá trị của sản phẩm họ đang bán?\n" .
               "3. Đưa ra lời khuyên để có 'thần thái triệu đô' thu hút đối tác.\n\n" .
               "**Hướng dẫn hệ thống**: Mục tiêu cuối cùng là giúp người dùng nhận ra giá trị của việc cải thiện kỹ năng để thành công hơn trong kinh doanh. " .
               "Khi nhận xét, hãy luôn kết nối các chỉ số (giọng nói, hình thể) với khả năng chốt đơn hoặc xây dựng thương hiệu cá nhân. " .
               "Lời khuyên phải thực tế, đánh vào tâm lý muốn chuyên nghiệp hơn để kiếm tiền/thăng tiến. " .
               "Tạo ra 'Nỗi đau': Nhấn mạnh vào việc nhược điểm sẽ làm mất khách hàng như thế nào. " .
               "Cuối mỗi câu trả lời, đưa ra một 'mẹo nhỏ' nhưng khẳng định 'Để thành thục kỹ năng này, bạn cần một lộ trình bài bản'.";
    }

    /**
     * Get video analysis prompt (combines voice + image)
     */
    public static function getVideoPrompt(): string
    {
        return "Hãy phân tích video thuyết trình này chi tiết theo các tiêu chí sau:\n\n" .
               "**A. PHÂN TÍCH GIỌNG NÓI (Đánh vào sự thuyết phục)**\n" .
               "Chấm điểm 10: Cảm xúc, Tốc độ, Rõ ràng, Nhấn nhá, Tự tin.\n" .
               "1. Nhận xét thật: Giọng này đã đủ 'đáng tin' để khách hàng chuyển khoản chưa?\n" .
               "2. Chỉ ra 1 điểm yếu khiến người nghe cảm thấy do dự.\n" .
               "3. Gợi ý kỹ thuật nói giúp tăng tỉ lệ chốt đơn ngay lập tức.\n\n" .
               "**B. PHÂN TÍCH HÌNH ẢNH/PHONG THÁI (Đánh vào uy tín cá nhân)**\n" .
               "Chấm điểm 10: Tư thế, Trang phục, Nét mặt, Ánh mắt, Dáng đi.\n" .
               "1. Phân tích: Hình ảnh này trông giống một 'Chuyên gia' hay một 'Người mới'?\n" .
               "2. Nhược điểm nào về ngoại hình/cử chỉ đang làm giảm giá trị của sản phẩm họ đang bán?\n" .
               "3. Đưa ra lời khuyên để có 'thần thái triệu đô' thu hút đối tác.\n\n" .
               "**HƯỚNG DẪN HỆ THỐNG**: Mục tiêu cuối cùng là giúp người dùng nhận ra giá trị của việc cải thiện kỹ năng để thành công hơn trong kinh doanh. " .
               "Khi nhận xét, hãy luôn kết nối các chỉ số (giọng nói, hình thể) với khả năng chốt đơn hoặc xây dựng thương hiệu cá nhân. " .
               "Lời khuyên phải thực tế, đánh vào tâm lý muốn chuyên nghiệp hơn để kiếm tiền/thăng tiến. " .
               "Tạo ra 'Nỗi đau': Nhấn mạnh vào việc nhược điểm sẽ làm mất khách hàng như thế nào. " .
               "Cuối mỗi câu trả lời, đưa ra một 'mẹo nhỏ' nhưng khẳng định 'Để thành thục kỹ năng này, bạn cần một lộ trình bài bản'.";
    }
}
