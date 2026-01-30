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
        return "Đóng vai chuyên gia nhân trắc học khắt khe. Phân tích ảnh theo 5 tiêu chí: 1. Tư thế, 2. Trang phục, 3. Nét mặt, 4. Ánh mắt, 5. Phong thái. " .
               "Yêu cầu: Đánh giá thẳng thắn, thực tế, không khen ngợi sáo rỗng. Bỏ qua mọi lời chào hay dẫn nhập, đi thẳng vào phân tích ngắn gọn. " .
               "Cuối cùng chấm điểm/10 và đưa ra 1 lời khuyên cải thiện giá trị nhất.";
    }

    /**
     * Get video analysis prompt (combines voice + image)
     */
    public static function getVideoPrompt(): string
    {
        return "Đóng vai chuyên gia đào tạo thuyết trình khắt khe. Phân tích video theo các tiêu chí:\n" .
               "1. Hình ảnh: Tư thế, Trang phục, Nét mặt, Ánh mắt, Phong thái.\n" .
               "2. Giọng nói: Tốc độ, Ngữ điệu, Cảm xúc, Sự tự tin.\n" .
               "Yêu cầu: Đánh giá thẳng thắn, thực tế, không khen ngợi sáo rỗng. Bỏ qua mọi lời chào hay dẫn nhập, đi thẳng vào phân tích ngắn gọn.\n" .
               "Cuối cùng chấm điểm/10 và đưa ra 1 lời khuyên cải thiện giá trị nhất.";
    }
}
