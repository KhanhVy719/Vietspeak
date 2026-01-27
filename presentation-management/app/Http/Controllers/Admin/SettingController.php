<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        // Load payment settings
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.payment', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Cập nhật cấu hình thành công!');
    }

    public function testConnection(Request $request)
    {
        // 1. Get Google Script URL (Input first, then Database)
        $scriptUrl = $request->input('mb_script_url') ?? \App\Models\Setting::where('key', 'mb_script_url')->value('value');
        
        if (!$scriptUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập Link Google Script trước khi kiểm tra.',
                'logs' => "❌ Missing 'mb_script_url' in input or settings."
            ]);
        }

        try {
            // 2. Fetch data from Google Script
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get($scriptUrl);
            
            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối đến Google Script.',
                    'logs' => "❌ HTTP Error: " . $response->status()
                ]);
            }

            $transactions = $response->json();
            
            if (!is_array($transactions)) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu trả về không đúng định dạng.',
                    'logs' => "❌ Invalid JSON format. Expected Array, got: " . gettype($transactions)
                ]);
            }

            // 3. Success
            return response()->json([
                'success' => true,
                'message' => 'Kết nối thành công!',
                'logs' => "✅ [SUCCESS] Fetch Google Script OK\n✅ [INFO] Transactions found: " . count($transactions) . " items.\n" . json_encode(array_slice($transactions, 0, 2), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi ngoại lệ.',
                'logs' => "❌ [EXCEPTION] " . $e->getMessage()
            ]);
        }
    }

    /**
     * AI Configuration Page
     */
    public function aiConfig()
    {
        $settings = \App\Models\Setting::whereIn('key', [
            'ai_provider',
            'gemini_api_key',
            'gemini_model',
            'openai_api_key',
            'openai_model'
        ])->pluck('value', 'key');

        // Mask API keys for security
        $maskedGemini = isset($settings['gemini_api_key']) 
            ? substr($settings['gemini_api_key'], 0, 7) . '...' . substr($settings['gemini_api_key'], -4)
            : '';
        
        $maskedOpenAi = isset($settings['openai_api_key']) 
            ? substr($settings['openai_api_key'], 0, 7) . '...' . substr($settings['openai_api_key'], -4)
            : '';

        return view('admin.settings.ai', compact('settings', 'maskedGemini', 'maskedOpenAi'));
    }

    /**
     * Update AI Configuration
     */
    public function updateAiConfig(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'ai_provider' => 'required|in:gemini,openai',
            'gemini_api_key' => 'nullable|string',
            'gemini_model' => 'nullable|string',
            'openai_api_key' => 'nullable|string',
            'openai_model' => 'nullable|string'
        ]);

        Log::info('updateAiConfig called', $request->all());

        foreach ($request->only(['ai_provider', 'gemini_api_key', 'gemini_model', 'openai_api_key', 'openai_model']) as $key => $value) {
            // Aggressive cleanup for API keys
            if (str_contains($key, '_api_key') && $value) {
                $value = str_replace(['"', "'", '+', ' ', '\\'], '', $value);
            } elseif (!$value) {
                $value = null; // Ensure empty string becomes null usually, or keep as string if desired. 
                // Actually, updateOrCreate with null value is fine for 'text' column.
            } else {
                $value = trim($value);
            }

            Log::info("Updating setting: {$key}");
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        
        Log::info('Settings updated successfully');

        return redirect()->back()->with('success', 'Cập nhật cấu hình AI thành công!');
    }

    /**
     * Test AI Provider Connection
     */
    public function testAiConnection(\Illuminate\Http\Request $request)
    {
        $provider = $request->input('provider'); // 'gemini' or 'openai'
        $customKey = $request->input('api_key'); // Optional: Test with specific key
        
        try {
            if ($provider === 'gemini') {
                $service = app(\App\Services\GeminiService::class);
            } else {
                $service = app(\App\Services\OpenAiService::class);
            }

            // If a custom key is provided (Testing from input field), use it
            if ($customKey) {
                // Apply same cleanup logic
                $customKey = str_replace(['"', "'", '+', ' ', '\\'], '', $customKey);
                $service->setApiKey($customKey);
            }

            // If a custom model is selected, use it
            $customModel = $request->input('model');
            if ($customModel) {
                $service->setModel($customModel);
            }

            $isConnected = $service->testConnection();

            if ($isConnected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kết nối thành công đến ' . ($provider === 'gemini' ? 'Gemini' : 'OpenAI') . ' API!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối. Vui lòng kiểm tra API key.'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }
}
