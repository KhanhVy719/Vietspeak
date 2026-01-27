<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        $currentApiKey = env('GEMINI_API_KEY', '');
        
        // Mask the API key for security (show first 6 and last 4 chars)
        $maskedKey = '';
        if (strlen($currentApiKey) > 10) {
            $maskedKey = substr($currentApiKey, 0, 6) . '****...****' . substr($currentApiKey, -4);
        } else {
            $maskedKey = '****';
        }
        
        return view('admin.settings.index', [
            'maskedApiKey' => $maskedKey,
            'hasApiKey' => !empty($currentApiKey)
        ]);
    }

    public function updateApiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string|min:20|regex:/^AIza.*/'
        ], [
            'api_key.required' => 'API Key không được để trống',
            'api_key.min' => 'API Key phải có ít nhất 20 ký tự',
            'api_key.regex' => 'API Key không hợp lệ (phải bắt đầu bằng "AIza")'
        ]);

        $apiKey = $request->input('api_key');

        try {
            // Update .env file
            $this->updateEnvFile('GEMINI_API_KEY', $apiKey);
            
            // Log the change
            Log::info('GEMINI_API_KEY updated by admin: ' . auth()->user()->email);
            
            // Clear config cache
            Artisan::call('config:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'API Key đã được cập nhật thành công!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update GEMINI_API_KEY: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật API Key. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function testApiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string'
        ]);

        $apiKey = $request->input('api_key');

        try {
            // Test API key by calling Gemini models list endpoint
            $response = Http::timeout(10)->get(
                "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}"
            );

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ API Key hợp lệ! Kết nối thành công.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => '❌ API Key không hợp lệ hoặc đã hết hạn.'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Không thể kết nối đến Gemini API. Kiểm tra internet.'
            ]);
        }
    }

    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');
        
        if (!file_exists($path)) {
            throw new \Exception('.env file not found');
        }

        $content = file_get_contents($path);
        
        // Check if key exists
        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing key
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                $content
            );
        } else {
            // Append new key
            $content .= "\n{$key}=\"{$value}\"\n";
        }
        
        file_put_contents($path, $content);
    }
}
