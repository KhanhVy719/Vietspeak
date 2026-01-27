<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cài Đặt Hệ Thống') }}
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
                                <a href="{{ route('admin.settings.index') }}" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                                    💳 Thanh toán (MBBank)
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="{{ route('admin.settings.ai-config') }}" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active">
                                    🤖 AI Configuration
                                </a>
                            </li>
                        </ul>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">🔑 Quản Lý API Key AI (Gemini)</h3>
                    
                    <!-- Success/Error Messages -->
                    <div id="messageContainer" class="mb-4 hidden">
                        <div id="messageBox" class="p-4 rounded-lg"></div>
                    </div>

                    <!-- Current API Key -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            API Key Hiện Tại:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   value="{{ $maskedApiKey }}" 
                                   disabled 
                                   class="flex-1 px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-mono">
                            @if($hasApiKey)
                                <span class="text-green-600 font-semibold">✓ Đã cấu hình</span>
                            @else
                                <span class="text-red-600 font-semibold">✗ Chưa cấu hình</span>
                            @endif
                        </div>
                    </div>

                    <!-- New API Key Form -->
                    <form id="apiKeyForm" onsubmit="updateApiKey(event)">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                API Key Mới:
                            </label>
                            <input type="text" 
                                   id="api_key" 
                                   name="api_key" 
                                   placeholder="AIzaSy..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                Nhập Gemini API Key mới (phải bắt đầu bằng "AIza" và có ít nhất 20 ký tự)
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="testApiKey()" 
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                🧪 Test API Key
                            </button>
                            
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                💾 Lưu Thay Đổi
                            </button>
                        </div>
                    </form>

                    <!-- Help Section -->
                    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">📘 Hướng Dẫn Lấy API Key:</h4>
                        <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
                            <li>Truy cập <a href="https://makersuite.google.com/app/apikey" target="_blank" class="underline">Google AI Studio</a></li>
                            <li>Đăng nhập với tài khoản Google</li>
                            <li>Click "Create API Key" hoặc "Get API Key"</li>
                            <li>Copy API Key và paste vào form trên</li>
                            <li>Click "Test API Key" để kiểm tra, sau đó "Lưu Thay Đổi"</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMessage(message, type = 'success') {
            const container = document.getElementById('messageContainer');
            const box = document.getElementById('messageBox');
            
            container.classList.remove('hidden');
            box.className = `p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'}`;
            box.textContent = message;
            
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }

        async function testApiKey() {
            const apiKey = document.getElementById('api_key').value;
            
            if (!apiKey) {
                showMessage('Vui lòng nhập API Key', 'error');
                return;
            }

            try {
                const response = await fetch('{{ route("admin.settings.test-api-key") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ api_key: apiKey })
                });

                const data = await response.json();
                showMessage(data.message, data.success ? 'success' : 'error');
                
            } catch (error) {
                showMessage('Lỗi kết nối. Vui lòng thử lại.', 'error');
            }
        }

        async function updateApiKey(event) {
            event.preventDefault();
            
            const apiKey = document.getElementById('api_key').value;
            
            if (!confirm('Bạn có chắc muốn thay đổi API Key? Hệ thống AI sẽ sử dụng key mới ngay lập tức.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("admin.settings.update-api-key") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ api_key: apiKey })
                });

                const data = await response.json();
                
                if (data.success) {
                    showMessage(data.message, 'success');
                    document.getElementById('api_key').value = '';
                    
                    // Reload page after 2 seconds to show updated masked key
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                }
                
            } catch (error) {
                showMessage('Lỗi kết nối. Vui lòng thử lại.', 'error');
            }
        }
    </script>
</x-app-layout>
