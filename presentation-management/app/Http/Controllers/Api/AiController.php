<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiProviderFactory;
use Illuminate\Support\Facades\Validator;

class AiController extends Controller
{
    // No longer inject specific service, use factory at runtime

    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string',
            'mime_type' => 'required|string',
            'data' => 'required|string',
            'model' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $prompt = $request->input('prompt');
        $mimeType = $request->input('mime_type');
        $base64Data = $request->input('data');

        // Simple security: allow only specific models to prevent abuse?
        // For now, trust the frontend (or default to flash)
        
        // Check Usage Fee (2000 VND) or AI Credits
        $user = $request->user();
        $FEE = 2000;
        $usedCredit = false;

        if (!$user) {
             return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Priority 1: Use AI Credits
        if ($user->ai_credits > 0) {
            $user->decrement('ai_credits');
            $usedCredit = true;
        } 
        // Priority 2: Use Balance
        else {
            if ($user->balance < $FEE) {
                return response()->json([
                    'error' => 'Số dư không đủ',
                    'message' => "Bạn không đủ số dư trong tài khoản. Bạn đã hết lượt AI miễn phí và cần nạp thêm " . number_format($FEE) . "đ để sử dụng tính năng này."
                ], 402);
            }
            $user->balance -= $FEE;
            $user->save();
        }

        // Use AI Provider Factory to get active service
        $aiService = AiProviderFactory::make();
        
        // Call AI service based on mime type
        if (str_starts_with($mimeType, 'audio/')) {
            $result = $aiService->analyzeAudio($base64Data, $prompt);
        } elseif (str_starts_with($mimeType, 'image/')) {
            $result = $aiService->analyzeImage($base64Data, $prompt);
        } else {
            return response()->json(['error' => 'Unsupported mime type'], 400);
        }

        if (isset($result['error'])) {
            // Refund
            if ($usedCredit) {
                $user->increment('ai_credits');
            } else {
                $user->balance += $FEE;
                $user->save();
            }
            return response()->json($result, 500);
        }
        
        // Add balance info to response so UI can update
        $result['new_balance'] = $user->balance;
        $result['new_ai_credits'] = $user->ai_credits;

        return response()->json($result);
    }

    public function analyzeVideo(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,avi,webm,mkv|max:102400', // 100MB max
        ]);

        $user = $request->user();
        $cost = 5000; // VND

        // Check sufficient balance (prioritize AI credits)
        if ($user->ai_credits < 1 && $user->balance < $cost) {
            return response()->json([
                'success' => false,
                'message' => 'Số dư không đủ! Vui lòng nạp thêm tiền hoặc mua thêm AI Credits.'
            ], 402)->header('Access-Control-Allow-Origin', '*');
        }

        $file = $request->file('file');
        
        // Read video file and encode to base64
        $videoData = base64_encode(file_get_contents($file->getRealPath()));

        // Prepare prompt for video analysis
        // Prepare prompt for video analysis
        $prompt = "Hãy phân tích video thuyết trình này chi tiết theo các tiêu chí sau:\n\n" .
                  "1. **Cảm xúc & Phong thái**: Đánh giá cảm xúc khuôn mặt, sự tự tin, phong thái (tư thế, ánh mắt, trang phục).\n" .
                  "2. **Giọng nói & Diễn đạt**: Tốc độ nói, sự rõ ràng, điểm nhấn nhá, và tông giọng.\n" .
                  "3. **Nội dung trình bày**: Cấu trúc, logic và sức thuyết phục.\n\n" .
                  "Chấm điểm tổng thể trên thang 10 và đưa ra 3-5 gợi ý cải thiện cụ thể để phát triển kỹ năng thuyết trình.";

        try {
            // Call OpenAI Video Analysis (frame extraction + GPT-4o Vision)
            // Use AI Provider Factory to get active service
            $aiService = AiProviderFactory::make();
            $result = $aiService->analyzeVideo($file->getRealPath(), $prompt);

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Lỗi khi phân tích video'
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }

            // Handle response format differences between Gemini and OpenAI
            $analysisText = '';
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                 // Gemini format
                 $analysisText = $result['candidates'][0]['content']['parts'][0]['text'];
            } elseif (isset($result['choices'][0]['message']['content'])) {
                 // OpenAI format
                 $analysisText = $result['choices'][0]['message']['content'];
            } else {
                 $analysisText = 'Không nhận được kết quả phân tích. (Unknown format)';
                 Log::warning('Unknown AI Response format:', (array)$result);
            }

            // Deduct cost (prioritize ai_credits first)
            $usedCredit = false;
            if ($user->ai_credits >= 1) {
                $user->decrement('ai_credits', 1);
                $usedCredit = true;
            } else {
                $user->decrement('balance', $cost);
            }

            // Save to history
            \Illuminate\Support\Facades\DB::table('ai_analysis_history')->insert([
                'user_id' => $user->id,
                'type' => 'video',
                'cost' => $usedCredit ? 0 : $cost, // 0 if used credit
                'prompt' => $prompt,
                'result' => $analysisText,
                'model' => AiProviderFactory::getProvider() . ':' . AiProviderFactory::getModel(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'result' => $analysisText,
                'cost' => $cost,
                'used_credit' => $usedCredit,
                'remaining_balance' => $user->fresh()->balance,
                'remaining_credits' => $user->fresh()->ai_credits
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gemini Video Analysis Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi phân tích video: ' . $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }
}
