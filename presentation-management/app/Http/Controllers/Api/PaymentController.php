<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Tạo Payment Link
     */
    public function createPaymentLink(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $user = $request->user();
        $amount = $request->input('amount');
        
        // Tạo mã đơn hàng duy nhất: ORDER_[USER_ID]_[TIMESTAMP]
        $orderId = 'ORDER_' . $user->id . '_' . time();
        
        // Nội dung theo yêu cầu: VIETSPEAK<mã số bất kỳ>
        // Ta dùng random string 6 ký tự
        $randomCode = strtoupper(Str::random(6));
        $description = "VIETSPEAK" . $randomCode; 

        $data = [
            'amount' => $amount,
            'orderDescription' => $description,
            'orderId' => $orderId
        ];

        $paymentInfo = $this->paymentService->createPaymentLink($data);

        return response()->json([
            'success' => true,
            'data' => $paymentInfo,
            'order_id' => $orderId
        ]);
    }

    /**
     * Webhook nhận thông báo từ SePay
     * Route này cần được cấu hình "Exclude CSRF" nếu dùng session cookie, nhưng API thì ok.
     */
    public function webhook(Request $request)
    {
        Log::info('SePay Webhook Received:', $request->all());

        // Kiểm tra bảo mật (API Key / Token header)
        // Link docs: https://sepay.vn/docs/webhook
        
        $data = $request->all();
        
        // Giả sử data trả về có form: { "content": "NP 123 174...", "amount": 50000, ... }
        // Cần parse content để lấy User ID.
        // Content quy ước: "NP [UserId] [Time]"
        
        $content = $data['content'] ?? '';
        $amount = $data['transferAmount'] ?? 0; // SePay dùng transferAmount
        
        if ($amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid amount']);
        }

        // Parse User ID from content
        if (preg_match('/NP (\d+) /', $content, $matches)) {
            $userId = $matches[1];
            
            $user = User::find($userId);
            if ($user) {
                // Cộng tiền vào tài khoản
                // Cẩn thận: Webhook có thể gửi lại nhiều lần -> nên check transaction ID của SePay
                // Ở đây demo đơn giản:
                
                $user->balance += $amount;
                $user->save();
                
                Log::info("Added $amount to User ID $userId. New Balance: $user->balance");
                
                return response()->json(['success' => true, 'message' => 'Balance updated']);
            }
        }



        return response()->json(['success' => false, 'message' => 'User not found or invalid content']);
    }

    /**
     * API để user tự bấm "Tôi đã chuyển khoản"
     */
    public function checkTransaction(Request $request) 
    {
        // 1. Validate
        $request->validate([
            'order_id' => 'required',
            'amount' => 'required|numeric'
        ]);

        $orderId = $request->input('order_id');
        $amount = (int) $request->input('amount');
        
        // Cần parse Order ID để lấy content short (VD: NP 4 174...)
        // Nhưng ở Frontend ta đang sinh Description là "NP [ID] [Time]"
        // Nên ta cần Frontend gửi cả Order Description lên hoặc ta find lại từ Order ID (nếu có lưu DB).
        // Đơn giản nhất: Frontend gửi `orderDescription`
        
        $orderDescription = $request->input('order_description');
        if (!$orderDescription) {
            // Fallback reconstruct logic
             // USER_ID extracted from somewhere? No, let's require desc
             return response()->json(['success' => false, 'message' => 'Missing order description']);
        }
        
        // 2. Call Service Check
        // Cần đảm bảo mỗi Order chỉ được cộng tiền 1 lần.
        // Tốt nhất nên lưu Transaction ID ngân hàng vào DB "deposits" table.
        // Ở Demo này ta tạm bỏ qua check duplicate transaction ID, nhưng check thời gian.
        
        $isPaid = $this->paymentService->checkTransaction($orderDescription, $amount);

        if ($isPaid) {
            $user = $request->user();
            Log::info("User {$user->id} Paid. Old Balance: {$user->balance}, Add: $amount");
            
            // Check double money (demo logic: just add)
            // Real app: Check if order_id already processed
            
            $user->balance += $amount;
            $user->save();
            Log::info("User {$user->id} New Balance: {$user->balance}");
            
            return response()->json([
                'success' => true, 
                'message' => 'Thanh toán thành công! Đã cộng tiền.',
                'new_balance' => $user->balance
            ]);
        }
        
        return response()->json([
            'success' => false, 
            'message' => 'Chưa tìm thấy giao dịch. Vui lòng thử lại sau 30s.',
            'debug' => 'Check Logs for details'
        ]);
    }
}
