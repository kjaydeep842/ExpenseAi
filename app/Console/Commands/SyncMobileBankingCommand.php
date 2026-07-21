<?php

namespace App\Console\Commands;

use App\Models\TransactionSms;
use App\Models\User;
use App\Services\AppNotificationParserService;
use Illuminate\Console\Command;

class SyncMobileBankingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:sync-mobile-banking {--phone= : Specific mobile phone number to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and process real pending mobile banking SMS logs for registered users every 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle(AppNotificationParserService $parser)
    {
        $phoneOption = $this->option('phone');

        $query = User::whereNotNull('phone');
        if ($phoneOption) {
            $query->where('phone', $phoneOption);
        }

        $users = $query->get();
        $this->info("Checking pending mobile banking notifications for " . $users->count() . " users...");

        $totalSynced = 0;
        foreach ($users as $user) {
            $pendingLogs = TransactionSms::where('user_id', $user->id)
                ->where('parsed_status', 'unparsed')
                ->get();

            foreach ($pendingLogs as $log) {
                $result = $parser->processPaymentAppAlert($user->id, $log->raw_body, $log->sender);
                if ($result['success']) {
                    $log->update(['parsed_status' => 'transaction_created']);
                    $this->info("✓ Synced real transaction of \${$result['amount']} for {$user->name} ({$user->phone})");
                    $totalSynced++;
                }
            }
        }

        $this->info("5-Minute Sync Complete: {$totalSynced} pending mobile transactions processed.");
        return Command::SUCCESS;
    }
}
