<?php

namespace App\Services;

use App\Services\MBBankService; // Custom Service for direct API
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $mbbank;
    protected $bankAccount;

    public function __construct(MBBankService $mbbank)
    {
        // Load settings from DB
        $this->bankAccount = \App\Models\Setting::where('key', 'bank_account_no')->value('value');
        $this->bankName = \App\Models\Setting::where('key', 'bank_name')->value('value') ?? 'MB';
        $this->accountName = \App\Models\Setting::where('key', 'bank_account_name')->value('value');
        
        $this->mbbank = $mbbank;
    }

    /**
     * Tạo QR Code (Vẫn dùng format VietQR chung)
     */
    public function createPaymentLink(array $data)
    {
        $account = $this->bankAccount; 
        
        // Fallback if no account configured
        if (!$account) {
            // Default or Error? For now let's use a placeholder or log error
            Log::warning('No bank account configured in settings');
        }

        $amount = $data['amount'];
        $content = $data['orderDescription'];
        
        // Format: https://api.vietqr.io/image/<BIN>-<ACC>-<TEMPLATE>.jpg?amount=<AMOUNT>&addInfo=<CONTENT>
        // MBBank BIN: 970422. If user changes bank, we might need lookup table or API.
        // For now assume MBBank (970422) as default per user request.
        $bin = '970422'; // Default MB
        
        // If bank is not MB, we ideally need to map Bank Name to BIN. 
        // Simple mapping for common banks:
        $binMap = [
            'MB' => '970422', 'MBBANK' => '970422',
            'VCB' => '970436', 'VIETCOMBANK' => '970436',
            'ACB' => '970416',
            'BIDV' => '970418',
            'VPB' => '970432', 'VPBANK' => '970432',
            'TCB' => '970407', 'TECHCOMBANK' => '970407'
        ];
        
        $bankKey = strtoupper(preg_replace('/[^A-Z]/', '', $this->bankName));
        if (isset($binMap[$bankKey])) {
            $bin = $binMap[$bankKey];
        }

        $template = 'gXxsubc'; // Template user requested ('print' style often used)
        
        $qrUrl = "https://api.vietqr.io/image/{$bin}-{$account}-{$template}.jpg?amount={$amount}&addInfo=" . urlencode($content) . "&accountName=" . urlencode($this->accountName);
        
        return [
            'qr_url' => $qrUrl,
            'payment_url' => $qrUrl, 
            'amount' => $amount,
            'description' => $content,
            'bank_info' => [
                'bank' => $this->bankName,
                'account' => $account,
                'name' => $this->accountName
            ]
        ];
    }

    /**
     * Kiểm tra giao dịch trực tiếp từ MBBank Account
     */
    public function checkTransaction($orderContent, $amount)
    {
        try {
            // Lấy lịch sử giao dịch qua Service tự viết
            $transactions = $this->mbbank->getHistory($this->bankAccount);
            
            if (empty($transactions)) {
                 Log::warning('MBBank: No history returned or Login failed');
                 return false;
            }

            // Duyệt tìm giao dịch khớp
            Log::info("Checking vs Order: " . $orderContent . " | Amount: " . $amount);
            
            foreach ($transactions as $trans) {
                // Check format data trả về từ API Service
                $creditAmount = isset($trans['creditAmount']) ? (int)$trans['creditAmount'] : 0;
                $description = $trans['description'] ?? '';

                Log::info("SCAN TX: " . $description . " | Amt: " . $creditAmount);

                if ($creditAmount == $amount) {
                    if (str_contains(strtoupper($description), strtoupper($orderContent))) {
                        Log::info("MATCH FOUND: " . $description);
                        return true;
                    }
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('MBBank Service Error: ' . $e->getMessage());
            return false;
        }
    }
}
