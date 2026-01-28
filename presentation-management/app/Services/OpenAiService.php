<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class OpenAiService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        // Try to get from database settings first, fallback to .env
        $dbKey = Setting::where('key', 'openai_api_key')->value('value');
        $envKey = env('OPENAI_API_KEY');
        
        if ($dbKey) {
            Log::info('OpenAiService: Loaded API key from Database');
            $this->apiKey = $dbKey;
        } else {
            Log::info('OpenAiService: Fallback to .env API key');
            $this->apiKey = $envKey;
        }
        
        $this->model = Setting::where('key', 'openai_model')->value('value') 
                       ?? env('OPENAI_MODEL', 'gpt-4o');
    }

    public function setApiKey($key)
    {
        $this->apiKey = $key;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Analyze audio file using Whisper for transcription + GPT-4o for analysis
     */
    public function analyzeAudio($audioBase64, $prompt)
    {
        try {
            // Step 1: Transcribe audio using Whisper
            $transcription = $this->transcribeAudio($audioBase64);
            
            if (isset($transcription['error'])) {
                return $transcription;
            }

            $text = $transcription['text'] ?? '';

            // Step 2: Analyze transcription with GPT-4o
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'Bạn là chuyên gia phân tích kỹ năng thuyết trình và giao tiếp.'
                ],
                [
                    'role' => 'user',
                    'content' => "Đây là bản ghi âm của một bài thuyết trình:\n\n\"{$text}\"\n\n{$prompt}"
                ]
            ];

            return $this->chat($messages);

        } catch (\Exception $e) {
            Log::error('OpenAI Audio Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'Lỗi phân tích',
                'message' => 'Không thể phân tích audio. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Transcribe audio using Whisper API
     */
    protected function transcribeAudio($audioBase64)
    {
        try {
            // Decode base64 to temp file (Whisper requires file upload)
            $audioData = base64_decode($audioBase64);
            $tempFile = tempnam(sys_get_temp_dir(), 'audio_') . '.mp3';
            file_put_contents($tempFile, $audioData);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->attach('file', file_get_contents($tempFile), 'audio.mp3')
              ->post("{$this->baseUrl}/audio/transcriptions", [
                  'model' => 'whisper-1',
                  'language' => 'vi'
              ]);

            unlink($tempFile); // Clean up

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Whisper API Error: ' . $response->body());
            return ['error' => 'Lỗi transcription'];

        } catch (\Exception $e) {
            Log::error('Whisper Exception: ' . $e->getMessage());
            return ['error' => 'Lỗi transcription'];
        }
    }

    /**
     * Analyze image using GPT-4o Vision
     */
    public function analyzeImage($imageBase64, $prompt)
    {
        try {
            $messages = [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$imageBase64}"
                            ]
                        ]
                    ]
                ]
            ];

            return $this->chat($messages);

        } catch (\Exception $e) {
            Log::error('OpenAI Image Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'Lỗi phân tích',
                'message' => 'Không thể phân tích ảnh. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Analyze video by extracting frames (Vision) and Audio (Whisper)
     */
    public function analyzeVideo($videoPath, $prompt)
    {
        try {
            // --- Phase 1: Audio Analysis (Whisper) ---
            $audioPath = $this->extractAudio($videoPath);
            $transcriptionText = "";

            if ($audioPath) {
                // Transcribe
                $transcriptionResult = $this->transcribeAudioFile($audioPath);
                $transcriptionText = $transcriptionResult['text'] ?? "Không thể phân tích âm thanh.";
                
                // Cleanup audio file
                @unlink($audioPath);
            }

            // --- Phase 2: Visual Analysis (Frame Extraction) ---
            // Extract fewer frames (3) and they will be resized to reduce token usage
            $frames = $this->extractVideoFrames($videoPath, 3);

            if (empty($frames)) {
                return ['error' => 'Không thể extract frames từ video'];
            }

            // --- Phase 3: Combine & Reasoning ---
            $finalPrompt = $prompt . "\n\n" .
                           "### THÔNG TIN BỔ SUNG TỪ VIDEO:\n" .
                           "1. **Nội dung lời nói (Transcription)**: \"{$transcriptionText}\"\n" .
                           "   (Hãy dùng nội dung này để đánh giá ngữ điệu, sự trôi chảy và từ ngữ).\n" .
                           "2. **Hình ảnh (Visuals)**: Xem các frame đính kèm để đánh giá ngôn ngữ cơ thể, trang phục, ánh mắt.\n\n" .
                           "Hãy kết hợp cả hai nguồn dữ liệu trên để chấm điểm và nhận xét chính xác.";

            // Build message with multiple images
            $content = [
                [
                    'type' => 'text',
                    'text' => $finalPrompt
                ]
            ];

            foreach ($frames as $frameBase64) {
                $content[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:image/jpeg;base64,{$frameBase64}",
                        'detail' => 'low' // Use 'low' detail to reduce tokens and avoid rejection
                    ]
                ];
            }

            $messages = [
                ['role' => 'user', 'content' => $content]
            ];

            return $this->chat($messages, 3000); // Increase max_tokens for detailed video analysis

        } catch (\Exception $e) {
            Log::error('OpenAI Video Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'Lỗi phân tích video',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Extract audio from video using FFmpeg
     */
    protected function extractAudio($videoPath)
    {
        try {
            $outputDir = sys_get_temp_dir();
            $outputPath = $outputDir . '/audio_' . uniqid() . '.mp3';

            // FFmpeg command to extract audio: -vn (no video), -acodec libmp3lame (mp3)
            // -y to overwrite
            $command = "ffmpeg -i \"{$videoPath}\" -vn -acodec libmp3lame -q:a 4 -y \"{$outputPath}\" 2>&1";
            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($outputPath)) {
                Log::error('FFmpeg Audio Extraction Error: ' . implode("\n", $output));
                return null;
            }

            return $outputPath;
        } catch (\Exception $e) {
            Log::error('Audio Extraction Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Transcribe audio file using Whisper API (File path version)
     */
    protected function transcribeAudioFile($filePath)
    {
        try {
            $response = Http::timeout(180)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->attach('file', file_get_contents($filePath), 'audio.mp3')
              ->post("{$this->baseUrl}/audio/transcriptions", [
                  'model' => 'whisper-1',
                  'language' => 'vi' // Optimize for Vietnamese
              ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Whisper API Error: ' . $response->body());
            return ['error' => 'Lỗi transcription'];

        } catch (\Exception $e) {
            Log::error('Whisper Exception: ' . $e->getMessage());
            return ['error' => 'Lỗi transcription'];
        }
    }

    /**
     * Extract frames from video file using FFmpeg
     */
    protected function extractVideoFrames($videoPath, $numFrames = 4)
    {
        $frames = [];
        $outputDir = sys_get_temp_dir() . '/video_frames_' . time();
        
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        try {
            // Use FFmpeg to extract frames
            // Extract 1 frame every N seconds is complex without duration.
            // Better strategy: select 'numFrames' evenly spaced.
            // But simple approach: 1 fps is too many.
            // Let's grab specific timestamps if possible, OR just grab 4 frames total using vf select.
            
            // Re-using previous logic but refined:
            // "select='not(mod(n,50))'" etc.
            // Simple: just dump frames at 1fps and take first 4? No.
            // Using logic: fps=1/2 means 1 frame every 2 sec.
            
            // Extract frames at lower resolution to reduce base64 size
            // scale=640:-1 means width=640px, height=auto (maintain aspect ratio)
            $command = "ffmpeg -i \"{$videoPath}\" -vf \"fps=1/3,scale=640:-1\" -frames:v {$numFrames} -q:v 5 \"{$outputDir}/frame_%03d.jpg\" 2>&1";
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('FFmpeg error: ' . implode("\n", $output));
                return [];
            }

            // Read frames and encode to base64
            $frameFiles = glob("{$outputDir}/frame_*.jpg");
            foreach ($frameFiles as $frameFile) {
                $frames[] = base64_encode(file_get_contents($frameFile));
                unlink($frameFile); // Clean up
            }

            rmdir($outputDir);

            return $frames;

        } catch (\Exception $e) {
            Log::error('Frame extraction error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * General chat completion
     */
    public function chat($messages, $maxTokens = 2000)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $maxTokens,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Format response similar to Gemini structure for compatibility
                return [
                    'candidates' => [
                        [
                            'content' => [
                                'parts' => [
                                    ['text' => $data['choices'][0]['message']['content'] ?? '']
                                ]
                            ]
                        ]
                    ]
                ];
            }

            Log::error('OpenAI Chat API Error: ' . $response->body());
            return [
                'error' => 'Lỗi máy chủ AI',
                'message' => 'Hệ thống AI đang gặp sự cố. Vui lòng thử lại sau.'
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI Chat Exception: ' . $e->getMessage());
            return [
                'error' => 'Lỗi kết nối',
                'message' => 'Không thể kết nối đến OpenAI. Vui lòng kiểm tra API key.'
            ];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $maskedKey = substr($this->apiKey, 0, 5) . '...' . substr($this->apiKey, -4);
            Log::info("Testing OpenAI Connection with Key: {$maskedKey}");

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => 'Say hello']
                ],
                'max_tokens' => 5
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI Connection Test Failed: ' . $response->body());
                throw new \Exception('API Error: ' . $response->status() . ' - ' . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            Log::error('OpenAI Connection Test Failed: ' . $e->getMessage());
            throw $e; // Re-throw to controller
        }
    }
}
