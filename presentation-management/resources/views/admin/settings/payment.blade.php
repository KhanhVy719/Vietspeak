<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cấu Hình Thanh Toán') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Navigation Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                            <li class="mr-2">
                                <a href="{{ route('admin.settings.index') }}" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active">
                                    💳 Thanh toán (MBBank)
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="{{ route('admin.settings.ai-config') }}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                                    🤖 AI Configuration
                                </a>
                            </li>
                        </ul>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">💳 Cấu Hình Thanh Toán MBBank</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-200 rounded-lg text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Payment Configuration Form -->
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Ngân hàng:
                                </label>
                                <input type="text" 
                                       id="bank_name" 
                                       name="bank_name" 
                                       value="{{ $settings['bank_name'] ?? 'MBBank' }}"
                                       placeholder="Ví dụ: MBBank" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Bank Account Number -->
                            <div>
                                <label for="bank_account_no" class="block text-sm font-medium text-gray-700 mb-2">
                                    Số Tài Khoản:
                                </label>
                                <input type="text" 
                                       id="bank_account_no" 
                                       name="bank_account_no" 
                                       value="{{ $settings['bank_account_no'] ?? '' }}"
                                       placeholder="Ví dụ: 0333..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Account Name -->
                            <div class="md:col-span-2">
                                <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên Chủ Tài Khoản:
                                </label>
                                <input type="text" 
                                       id="bank_account_name" 
                                       name="bank_account_name" 
                                       value="{{ $settings['bank_account_name'] ?? '' }}"
                                       placeholder="NGUYEN VAN A" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                            </div>
                        </div>

                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <label for="mb_script_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Google Apps Script URL (Auto-Check):
                            </label>
                            <input type="url" 
                                   id="mb_script_url" 
                                   name="mb_script_url" 
                                   value="{{ $settings['mb_script_url'] ?? '' }}"
                                   placeholder="https://script.google.com/macros/s/..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                URL của Google Apps Script để lấy dữ liệu giao dịch MBBank
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="testConnection()" 
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                🧪 Test Kết Nối
                            </button>
                            
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                💾 Lưu Cấu Hình
                            </button>
                        </div>
                    </form>

                    <!-- Test Result Section -->
                    <div id="testResult" class="hidden mt-6 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2">Kết Quả Test:</h4>
                        <pre id="testLogs" class="bg-gray-800 text-white p-3 rounded text-sm overflow-auto max-h-64"></pre>
                    </div>

                    <!-- Help Section -->
                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">📘 Hướng Dẫn Cấu Hình:</h4>
                        <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                            <li>Tạo Google Apps Script để lấy dữ liệu từ MBBank</li>
                            <li>Deploy script dưới dạng Web App</li>
                            <li>Copy URL deployment và paste vào form trên</li>
                            <li>Click "Test Kết Nối" để kiểm tra</li>
                            <li>Nếu thành công, click "Lưu Cấu Hình"</li>
                        </ol>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-sm text-yellow-800">
                                <strong>Lưu ý:</strong> Script phải trả về JSON array chứa danh sách giao dịch
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function testConnection() {
            const url = document.getElementById('mb_script_url').value;
            
            if (!url) {
                alert('Vui lòng nhập Google Apps Script URL');
                return;
            }

            const resultDiv = document.getElementById('testResult');
            const logsDiv = document.getElementById('testLogs');
            
            resultDiv.classList.remove('hidden');
            resultDiv.className = 'mt-6 p-4 rounded-lg bg-gray-100';
            logsDiv.textContent = '⏳ Đang test kết nối...';

            try {
                const response = await fetch('{{ route("admin.settings.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ mb_script_url: url })
                });

                const data = await response.json();
                
                if (data.success) {
                    resultDiv.className = 'mt-6 p-4 rounded-lg bg-green-100 border border-green-200';
                    logsDiv.textContent = data.logs;
                } else {
                    resultDiv.className = 'mt-6 p-4 rounded-lg bg-red-100 border border-red-200';
                    logsDiv.textContent = data.logs;
                }
                
            } catch (error) {
                resultDiv.className = 'mt-6 p-4 rounded-lg bg-red-100 border border-red-200';
                logsDiv.textContent = '❌ Lỗi kết nối: ' + error.message;
            }
        }
    </script>
</x-app-layout>
