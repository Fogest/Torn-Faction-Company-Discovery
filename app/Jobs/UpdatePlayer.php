<?php

namespace App\Jobs;

use App\Company;
use App\Jobs\Middleware\RateLimited;
use App\Player;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdatePlayer
 * @package App\Jobs
 * Updates the player data from the Torn API
 */
class UpdatePlayer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $player;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new RateLimited];
    }


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withOptions(
            [
                'verify' => false,
                'base_uri' => config('custom.torn_api_base'),
                'timeout' => 5.0
            ]
        )->get(
            "user/" . $this->player->id,
            [
                'selections' => 'profile',
                'key' => config('custom.torn_api_key')
            ]
        );
        $tornPlayerData = $response->json();

        $company = Company::where('player_id', $this->player->id);
        if ($company && $tornPlayerData['job']['position'] === "Director") {
            if ($company->id != $tornPlayerData['job']['company_id']) {
                $company->delete();
            }
            $company = Company::updateOrCreate(
                [
                    'id' => $tornPlayerData['job']['company_id']
                ],
                [
                    'name' => $tornPlayerData['job']['company_name'],
                    'player_id' => $this->player->id,
                    'isOwner' => true
                ]
            );
            Log::info("Updating company '{$company->name}'' now", ['company' => $company]);
            UpdateCompany::dispatch($this->player, $company);
        } elseif ($company) {
            $company->delete();
        }

        $this->player->last_complete_update_at = Carbon::now();
        $this->player->save();
    }
}
