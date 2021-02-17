<?php

namespace App\Jobs\Recruiting;

use App\PlayerRecruit;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BatchUpdatePlayers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;

    protected $recruit;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(20);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recruits = PlayerRecruit::where(
            'updated_at',
            '<',
            Carbon::now()->subDays(7)
        )->limit(30)->get();

        Log::info(
            "Batch Updating Players now",
            ['recruits' => $recruits]
        );
        foreach ($recruits as $recruit) {
            UpdateRecruitPlayer::dispatch($recruit);
        }
    }
}
