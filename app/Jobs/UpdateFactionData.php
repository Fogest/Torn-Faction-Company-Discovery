<?php

namespace App\Jobs;

use App\Faction;
use App\Jobs\Middleware\RateLimited;
use App\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateFactionData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $faction;

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
     * @param Faction $faction
     */
    public function __construct(Faction $faction)
    {
        $this->faction = $faction;
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
            "faction/" . $this->faction->id,
            [
                'selections' => 'basic',
                'key' => config('custom.torn_api_key')
                ]
        );

        if ($response->failed()) {
            Log::error('Failed to connect to Torn API', ['response' => $response,
                'faction' => $this->faction]);
            return;
        }

        Log::debug('Response', ['response' => $response]);

        $tornFactionData = $response->json();
        Log::debug('Response in JSON', ['response_json' => $tornFactionData]);
        Log::debug('Name from JSON', ['name' => $tornFactionData['name']]);

        $this->faction->name = $tornFactionData['name'];

        $this->faction->current_players = count($tornFactionData['members']);

        if ($this->faction->isDirty('current_players')) {
            $this->faction->save();
        }

        // Go through "members" in API response to generate Player "collection"
        $players = [];
        foreach ($tornFactionData['members'] as $memberId => $member) {
            $player = new Player;
            $player->id = $memberId;
            $player->faction_id = $this->faction->id;
            $player->name = $member['name'];
            $players[] = $player;
        }
        UpdateFactionPlayerlist::dispatch($this->faction, collect($players));
    }
}
