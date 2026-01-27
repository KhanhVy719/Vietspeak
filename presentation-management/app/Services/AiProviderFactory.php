<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AiProviderFactory
{
    /**
     * Get the active AI service based on admin settings
     */
    public static function make()
    {
        $provider = Setting::where('key', 'ai_provider')->value('value') ?? 'openai';
        Log::info("AiProviderFactory: Active Provider from DB is '{$provider}'");

        return match($provider) {
            'gemini' => app(GeminiService::class),
            'openai' => app(OpenAiService::class),
            default => app(OpenAiService::class),
        };
    }

    /**
     * Get active provider name
     */
    public static function getProvider(): string
    {
        return Setting::where('key', 'ai_provider')->value('value') ?? 'openai';
    }

    /**
     * Get model name for current provider
     */
    public static function getModel(): string
    {
        $provider = self::getProvider();
        
        return match($provider) {
            'gemini' => Setting::where('key', 'gemini_model')->value('value') ?? 'gemini-2.0-flash-exp',
            'openai' => Setting::where('key', 'openai_model')->value('value') ?? 'gpt-4o',
            default => 'gpt-4o',
        };
    }
}
