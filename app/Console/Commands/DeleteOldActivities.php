<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeleteOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:clean';

    protected $description = 'Delete activity logs older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $cutoffDate = Carbon::now()->subDays(7);

        $deleted = Activity::where('created_at', '<', $cutoffDate)->delete();
        Log::info("Deleted $deleted activity log(s) older than 7 days.");
        $this->info("Deleted $deleted activity log(s) older than 7 days.");
    }
}
