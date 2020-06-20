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
                'base_uri' => env('TORN_API_BASE', "http://api.torn.com/"),
                'timeout' => 5.0
            ]
        )->get(
            "company/" . $this->company->id,
            [
                'selections' => 'profile',
                'key' => env('TORN_API_KEY')
            ]
        );
        $tornCompanyData = $response->json()['company'];

        $this->company->company_type = $tornCompanyData['company_type'];
        $this->company->rank = $tornCompanyData['rating'];
        $this->company->hired_employees = $tornCompanyData['employees_hired'];
        $this->company->max_employees = $tornCompanyData['employees_capacity'];
        $this->company->save();

        Log::info("Finished updating company '{$this->company->name}' complete", ['company' => $this->company]);
    }
}
