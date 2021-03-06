<?php

namespace App\Jobs;

use App\Company;
use App\Jobs\Middleware\RateLimited;
use App\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $player;
    protected $company;

    public $timeout = 1200;

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
     * @param Player $player
     * @param Company $company
     */
    public function __construct(Player $player, Company $company)
    {
        $this->player = $player;
        $this->company = $company;
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
            "company/" . $this->company->id,
            [
                'selections' => 'profile',
                'key' => config('custom.torn_api_key')
            ]
        );
        $tornCompanyData = $response->json()['company'];
        $company = Company::where('player_id', $this->player->id)->first();

        $company->company_type = $tornCompanyData['company_type'];
        $company->rank = $tornCompanyData['rating'];
        $company->hired_employees = $tornCompanyData['employees_hired'];
        $company->max_employees = $tornCompanyData['employees_capacity'];
        $company->save();

        Log::info("Finished updating extra company '{$this->company->name}' complete", ['company' => $this->company]);
    }
}
