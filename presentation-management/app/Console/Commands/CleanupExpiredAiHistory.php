<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredAiHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:ai-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired AI analysis history records (GDPR compliance - 30 day retention)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired AI analysis history...');

        $deleted = DB::table('ai_analysis_history')
            ->where('expires_at', '<=', now())
            ->delete();

        $this->info("Deleted {$deleted} expired AI analysis records.");
        Log::info("AI History Cleanup: Deleted {$deleted} expired records.");

        return 0;
    }
}
