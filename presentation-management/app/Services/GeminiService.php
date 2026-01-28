<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class GeminiService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        // Try to get from database settings first, fallback to .env
        $this->apiKey = Setting::where('key', 'gemini_api_key')->value('value') 
                        ?? env('GEMINI_API_KEY');
        
        $this->model = Setting::where('key', 'gemini_model')->value('value') 
                       ?? env('GEMINI_MODEL', 'gemini-2.0-flash-exp');

        // Debug logging
        if ($this->apiKey) {
             Log::info('GeminiService: Loaded API Key (Masked): ' . substr($this->apiKey, 0, 5) . '...');
        } else {
             Log::error('GeminiService: API Key is MISSING or NULL');
        }
        Log::info('GeminiService: Loaded Model: ' . $this->model);
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
     * Analyze audio using Gemini
     */
    public function analyzeAudio($audioBase64, $prompt)
    {
        return $this->generateContent($this->model, $prompt, 'audio/mp3', $audioBase64);
    }

    /**
     * Analyze image using Gemini Vision
     */
    public function analyzeImage($imageBase64, $prompt)
    {
        return $this->generateContent($this->model, $prompt, 'image/jpeg', $imageBase64);
    }

    /**
     * Analyze video using Gemini 2.0 native video support
     */
    public function analyzeVideo($videoPath, $prompt)
    {
        try {
            $videoData = base64_encode(file_get_contents($videoPath));
            $mimeType = mime_content_type($videoPath);

            $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::withoutVerifying()->timeout(300)->post($url, [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $videoData
                            ]
                        ]
                    ]
                ]]
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Gemini Video Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'Lỗi phân tích video',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * General content generation
     */
    public function generateContent($model, $prompt, $mimeType, $base64Data)
    {
        $url = "{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Data
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Gemini API Error: ' . $response->body());
            return [
                'error' => 'Lỗi máy chủ',
                'message' => 'Hệ thống AI đang gặp sự cố. Vui lòng thử lại sau ít phút.'
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return [
                'error' => 'Lỗi máy chủ',
                'message' => 'Không thể kết nối đến hệ thống AI. Vui lòng kiểm tra kết nối mạng và thử lại.'
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
            Log::info("Testing Gemini Connection with Key: {$maskedKey}");

            $url = "{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}";
            
            $response = Http::withoutVerifying()->post($url, [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => 'Say ok']]
                ]]
            ]);

            if (!$response->successful()) {
                Log::error('Gemini Connection Test Failed: ' . $response->body());
                throw new \Exception('API Error: ' . $response->status() . ' - ' . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Gemini Connection Test Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
