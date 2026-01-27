<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisFacade;

class DebugConfig extends Command
{
    protected $signature = 'debug:config';
    protected $description = 'Debug system configuration and show potential issues';

    public function handle()
    {
        $this->info('=== DEBUGGING LARAVEL CONFIGURATION ===');
        $this->newLine();

        // 1. Session Configuration
        $this->line('📍 SESSION CONFIGURATION:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['SESSION_DRIVER (.env)', env('SESSION_DRIVER', 'NOT SET')],
                ['session.driver (config)', config('session.driver')],
                ['SESSION_STORE (.env)', env('SESSION_STORE', 'NOT SET')],
                ['session.store (config)', config('session.store')],
            ]
        );

        // 2. Cache Configuration
        $this->line('💾 CACHE CONFIGURATION:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['CACHE_STORE (.env)', env('CACHE_STORE', 'NOT SET')],
                ['cache.default (config)', config('cache.default')],
                ['CACHE_PREFIX (.env)', env('CACHE_PREFIX', 'NOT SET')],
            ]
        );

        // 3. Database Configuration
        $this->line('🗄️ DATABASE CONFIGURATION:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['DB_CONNECTION (.env)', env('DB_CONNECTION', 'NOT SET')],
                ['database.default (config)', config('database.default')],
                ['DB_HOST (.env)', env('DB_HOST', 'NOT SET')],
                ['DB_PORT (.env)', env('DB_PORT', 'NOT SET')],
                ['DB_DATABASE (.env)', env('DB_DATABASE', 'NOT SET')],
            ]
        );

        // 4. Redis Configuration
        $this->line('🔴 REDIS CONFIGURATION:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['REDIS_HOST (.env)', env('REDIS_HOST', 'NOT SET')],
                ['REDIS_PORT (.env)', env('REDIS_PORT', 'NOT SET')],
                ['REDIS_CLIENT (.env)', env('REDIS_CLIENT', 'NOT SET')],
            ]
        );

        // 5. PHP Extensions
        $this->line('🔌 PHP EXTENSIONS:');
        $this->table(
            ['Extension', 'Status'],
            [
                ['pdo_mysql', extension_loaded('pdo_mysql') ? '✅ Loaded' : '❌ Not Loaded'],
                ['pdo_pgsql', extension_loaded('pdo_pgsql') ? '✅ Loaded' : '❌ Not Loaded'],
                ['redis', extension_loaded('redis') ? '✅ Loaded' : '❌ Not Loaded'],
                ['opcache', extension_loaded('opcache') ? '✅ Loaded' : '❌ Not Loaded'],
            ]
        );

        // 6. Storage Directories
        $this->line('📁 STORAGE DIRECTORIES:');
        $paths = [
            'storage/framework/cache' => storage_path('framework/cache'),
            'storage/framework/sessions' => storage_path('framework/sessions'),
            'storage/framework/views' => storage_path('framework/views'),
            'storage/logs' => storage_path('logs'),
        ];

        $dirData = [];
        foreach ($paths as $name => $path) {
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            $dirData[] = [
                $name,
                $exists ? '✅ Exists' : '❌ Missing',
                $writable ? '✅ Writable' : '❌ Not Writable',
            ];
        }
        $this->table(['Directory', 'Exists', 'Writable'], $dirData);

        // 7. Test Connections
        $this->newLine();
        $this->line('🔍 CONNECTION TESTS:');
        
        // Test Database
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database Connection: SUCCESS');
        } catch (\Exception $e) {
            $this->error('❌ Database Connection: FAILED - ' . $e->getMessage());
        }

        // Test Cache
        try {
            Cache::put('test_key', 'test_value', 10);
            $value = Cache::get('test_key');
            if ($value === 'test_value') {
                $this->info('✅ Cache System: SUCCESS');
            } else {
                $this->warn('⚠️ Cache System: Partial - Value mismatch');
            }
            Cache::forget('test_key');
        } catch (\Exception $e) {
            $this->error('❌ Cache System: FAILED - ' . $e->getMessage());
        }

        // Test Redis (if configured)
        if (config('cache.default') === 'redis' || config('session.driver') === 'redis') {
            try {
                $redis = RedisFacade::connection();
                $redis->ping();
                $this->info('✅ Redis Connection: SUCCESS');
            } catch (\Exception $e) {
                $this->error('❌ Redis Connection: FAILED - ' . $e->getMessage());
            }
        }

        // 8. Potential Issues
        $this->newLine();
        $this->line('⚠️ POTENTIAL ISSUES:');
        $issues = [];

        if (config('session.driver') === 'redis' && !extension_loaded('redis')) {
            $issues[] = 'Session driver is Redis but Redis extension not loaded';
        }

        if (config('cache.default') === 'redis' && !extension_loaded('redis')) {
            $issues[] = 'Cache driver is Redis but Redis extension not loaded';
        }

        if (env('SESSION_DRIVER') !== config('session.driver')) {
            $issues[] = sprintf(
                'SESSION_DRIVER mismatch: .env="%s" vs config="%s"',
                env('SESSION_DRIVER'),
                config('session.driver')
            );
        }

        if (env('CACHE_STORE') !== config('cache.default')) {
            $issues[] = sprintf(
                'CACHE_STORE mismatch: .env="%s" vs config="%s"',
                env('CACHE_STORE'),
                config('cache.default')
            );
        }

        if (empty($issues)) {
            $this->info('✅ No issues detected!');
        } else {
            foreach ($issues as $issue) {
                $this->error('❌ ' . $issue);
            }
        }

        $this->newLine();
        $this->info('=== DEBUG COMPLETE ===');
        $this->line('Hint: Run "php artisan config:clear" to clear configuration cache');
        $this->line('Hint: Run "php artisan optimize:clear" to clear all caches');

        return 0;
    }
}
