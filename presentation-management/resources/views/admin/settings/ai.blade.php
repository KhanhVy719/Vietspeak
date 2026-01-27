<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('⚙️ Cấu hình AI Provider') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update-ai-config') }}" method="POST">
                        @csrf

                        <!-- Provider Selection -->
                        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                            <h5 class="font-bold text-lg mb-3">🤖 Chọn AI Provider</h5>
                            <div class="form-group">
                                <label class="block mb-2 font-medium">Provider đang sử dụng:</label>
                                <select name="ai_provider" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="gemini" {{ ($settings['ai_provider'] ?? 'openai') == 'gemini' ? 'selected' : '' }}>
                                        Google Gemini
                                    </option>
                                    <option value="openai" {{ ($settings['ai_provider'] ?? 'openai') == 'openai' ? 'selected' : '' }}>
                                        OpenAI (GPT-4o)
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Chọn provider sẽ được sử dụng cho tất cả phân tích AI.</p>
                            </div>
                        </div>

                        <!-- Gemini Configuration -->
                        <div class="mb-6 p-4 border rounded-lg">
                            <h5 class="font-bold text-lg mb-3 text-green-700">🟢 Google Gemini</h5>
                            
                            <div class="mb-4">
                                <label class="block mb-2 font-medium">Gemini API Key:</label>
                                <div class="flex gap-2">
                                    <input type="password" 
                                           name="gemini_api_key" 
                                           id="gemini_api_key"
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                           placeholder="{{ $maskedGemini ?: 'AIzaSy...' }}"
                                           value="{{ old('gemini_api_key') }}">
                                    <button type="button" onclick="document.getElementById('gemini_api_key').value = ''; document.getElementById('gemini_api_key').focus();" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md" title="Xóa trắng để gỡ key">
                                        🗑️
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Lấy API key tại: <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-blue-600 hover:underline">Google AI Studio</a>
                                </p>
                            </div>

                                <label class="block mb-2 font-medium">Gemini Model:</label>
                                <select name="gemini_model" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="gemini-1.5-flash" {{ ($settings['gemini_model'] ?? '') == 'gemini-1.5-flash' ? 'selected' : '' }}>
                                        Gemini 1.5 Flash (Recommended - Fastest & High limits)
                                    </option>
                                    <option value="gemini-2.0-flash-exp" {{ ($settings['gemini_model'] ?? '') == 'gemini-2.0-flash-exp' ? 'selected' : '' }}>
                                        Gemini 2.0 Flash Experimental (Next Gen - Low limits)
                                    </option>
                                    <option value="gemini-2.5-flash" {{ ($settings['gemini_model'] ?? '') == 'gemini-2.5-flash' ? 'selected' : '' }}>
                                        Gemini 2.5 Flash (Newest)
                                    </option>
                                </select>
                            </div>

                            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition" onclick="testConnection('gemini')">
                                🧪 Test Gemini Connection
                            </button>
                        </div>

                        <!-- OpenAI Configuration -->
                        <div class="mb-6 p-4 border rounded-lg">
                            <h5 class="font-bold text-lg mb-3 text-blue-700">🔵 OpenAI</h5>
                            
                            <div class="mb-4">
                                <label class="block mb-2 font-medium">OpenAI API Key:</label>
                                <div class="flex gap-2">
                                    <input type="password" 
                                           name="openai_api_key" 
                                           id="openai_api_key"
                                           class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                           placeholder="{{ $maskedOpenAi ?: 'sk-...' }}"
                                           value="{{ old('openai_api_key') }}">
                                    <button type="button" onclick="document.getElementById('openai_api_key').value = ''; document.getElementById('openai_api_key').focus();" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md" title="Xóa trắng để gỡ key">
                                        🗑️
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Lấy API key tại: <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">OpenAI Platform</a>
                                    <br><span class="text-xs text-red-500">* Để xóa key: Nhấn nút thùng rác rồi bấm "Lưu cấu hình".</span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-medium">OpenAI Model:</label>
                                <select name="openai_model" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="gpt-4o" {{ ($settings['openai_model'] ?? '') == 'gpt-4o' ? 'selected' : '' }}>
                                        GPT-4o (Best Quality)
                                    </option>
                                    <option value="gpt-4o-mini" {{ ($settings['openai_model'] ?? '') == 'gpt-4o-mini' ? 'selected' : '' }}>
                                        GPT-4o Mini (Faster & Cheaper)
                                    </option>
                                    <option value="gpt-4-turbo" {{ ($settings['openai_model'] ?? '') == 'gpt-4-turbo' ? 'selected' : '' }}>
                                        GPT-4 Turbo
                                    </option>
                                </select>
                            </div>

                            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition" onclick="testConnection('openai')">
                                🧪 Test OpenAI Connection
                            </button>
                        </div>

                        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-lg">
                            <strong>💡 Lưu ý:</strong>
                            <ul class="list-disc ml-5 mt-2">
                                <li>Gemini: Hỗ trợ video trực tiếp (miễn phí cao).</li>
                                <li>OpenAI: Phân tích video qua frame extraction (chi phí cao hơn).</li>
                                <li>API keys được lưu an toàn trong database.</li>
                            </ul>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition shadow-md">
                                💾 Lưu Cấu Hình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    async function testConnection(provider) {
        const button = event.target;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '⏳ Đang test...';

        try {
            // Get the current key value from input to test what user typed
            let apiKey = '';
            // Note: input IDs were added in previous step
            if (provider === 'gemini') {
                apiKey = document.getElementById('gemini_api_key').value;
            } else {
                apiKey = document.getElementById('openai_api_key').value;
            }

            const response = await fetch('{{ route("admin.settings.test-ai-connection") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    provider: provider,
                    api_key: apiKey 
                })
            });

            const data = await response.json();
            
            if (data.success) {
                alert('✅ ' + data.message);
            } else {
                alert('❌ ' + data.message);
            }
        } catch (error) {
            alert('❌ Lỗi kết nối: ' + error.message);
        } finally {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
    </script>
</x-app-layout>
