<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Models\Setting;

class MBBankService
{
    protected $username;
    protected $password;
    protected $deviceId;
    protected $sessionId;

    public function __construct()
    {
        // Get config from Database Settings
        $this->username = Setting::where('key', 'mb_user')->value('value');
        $this->password = Setting::where('key', 'mb_password')->value('value');
        // Default device ID or from setting
        $this->deviceId = env('MB_DEVICE_ID', 'rm0824e84489a2s'); 
    }

    public function login()
    {
        // Try to get session from cache first
        if (Cache::has('mb_session_id')) {
            $this->sessionId = Cache::get('mb_session_id');
            return true;
        }

        try {
            // Simplified Login Logic (mimicking web app or common wrappers)
            // Note: Endpoints might change. This uses a common known endpoint.
            $response = Http::withOptions(['verify' => false])->withHeaders([
                'Authorization' => 'Basic QURNSU46QURNSU4=', // Common basic auth for starting
                'Content-Type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Origin' => 'https://online.mbbank.com.vn',
                'Referer' => 'https://online.mbbank.com.vn/'
            ])->post('https://online.mbbank.com.vn/api/retail/user/login', [
                'userId' => $this->username,
                'password' => md5($this->password),
                'captcha' => '', 
                'sessionId' => null,
                'refNo' => time() // Random ref
            ]);

            // Note: This endpoint often requires Captcha now. 
            // If it fails with captcha required, we can't proceed easily without a solver.
            // But we will implement the structure.

            $data = $response->json();

            if (isset($data['sessionId'])) {
                $this->sessionId = $data['sessionId'];
                Cache::put('mb_session_id', $this->sessionId, 600); // 10 mins
                return true;
            }
            
            Log::error('MBBank Login Failed', $data);
            return false;

        } catch (\Exception $e) {
            Log::error('MBBank Connection Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getHistory($accountNo = null)
    {
        // 1. Get Script URL
        $scriptUrl = \App\Models\Setting::where('key', 'mb_script_url')->value('value');
        
        if (!$scriptUrl) {
            \Illuminate\Support\Facades\Log::error('MBBank: Missing Script URL');
            return [];
        }

        try {
            // 2. Call Google Script (Bypass SSL for local dev)
            $response = Http::withoutVerifying()->get($scriptUrl);
            
            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('MBBank: Script Error ' . $response->status());
                return [];
            }
            
            $rawTransactions = $response->json();
            
            if (!is_array($rawTransactions)) {
                 return [];
            }
            
            // 3. Map Data (Google Script Vietnamese Keys -> Internal Keys)
            $mappedTransactions = [];
            foreach ($rawTransactions as $tx) {
                $rawAmount = $tx['Số tiền'] ?? 0;
                // Clean amount if string
                if (is_string($rawAmount)) {
                    $rawAmount = (float)str_replace([',', '.'], '', $rawAmount);
                }

                $mappedTransactions[] = [
                    'transactionDate' => $tx['Ngày giao dịch'] ?? '',
                    'accountNo' => $tx['Số tài khoản'] ?? '',
                    'creditAmount' => $tx['Loại'] == 'Tiền vào' ? $rawAmount : 0,
                    'debitAmount' => $tx['Loại'] == 'Tiền ra' ? $rawAmount : 0,
                    'currency' => 'VND',
                    'description' => $tx['Nội dung thanh toán'] ?? '',
                    'availableBalance' => $tx['Lũy kế'] ?? 0,
                    'refNo' => $tx['Mã tham chiếu'] ?? ''
                ];
            }

            return $mappedTransactions;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('MBBank Exception: ' . $e->getMessage());
            return []; // Return empty array on failure
        }
    }


}
