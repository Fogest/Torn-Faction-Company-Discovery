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

        $company = Company::where('player_id', $this->player->id)->first();
        if ($company && isset($company->id)) {
            // Player has company, current API company id diff from DB ID,
            // delete DB company and treat as "new" company
            if ($company->id != $tornPlayerData['job']['company_id']) {
                $company->delete();
                Log::info("Deleting company '{$company->name}'' (Company ID Stale in DB)", ['company' => $company]);
            }
        }
        if ($tornPlayerData['job']['position'] === "Director") {
            // Company exists and director, update & save company.
            if ($company && isset($company->id)) {
                $company['name'] = $tornPlayerData['job']['company_name'];
                $company['player_id'] = $this->player->id;
                $company['isOwner'] = true;
                $company->save();
                Log::info("Updating company '{$company->name}'' now", ['company' => $company]);
            } else {
                // Is a director, no company in DB, create it!
                $company = new Company();
                $company['id'] = $tornPlayerData['job']['company_id'];
                $company['name'] = $tornPlayerData['job']['company_name'];
                $company['player_id'] = $this->player->id;
                $company['isOwner'] = true;
                $company->save();
                Log::info("Creating company '{$company->name}'' now", ['company' => $company]);
            }
            UpdateCompany::dispatch($this->player, $company);
        } elseif ($company) {
            $company->delete();
            Log::info("Deleting company '{$company->name}'' (no longer director)", ['company' => $company]);
        }

        $this->player->last_complete_update_at = Carbon::now();
        $this->player->save();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("The job failed :(", ['exception' => $exception]);
    }
}
