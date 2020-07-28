<?php

namespace App\Jobs;

use App\Faction;
use App\Jobs\Middleware\RateLimited;
use App\Player;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UpdateFactionPlayerlist implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $faction;
    protected $players;

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
     * @param Faction $faction The faction the players are in
     * @param Collection $players A collection of Player Eloquent
     *                            objects in a faction
     */
    public function __construct(Faction $faction, Collection $players)
    {
        $this->faction = $faction;
        $this->players = $players;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updatedPlayerIds = [];
        foreach ($this->players as $player) {
            $playerModel = Player::where('id', $player['id'])->first();
            if ($player && isset($player->id)) {
                // Player exists, lets update data
                $playerModel['faction_id'] = $player['faction_id'];
                $playerModel['name'] = $player['name'];
                $playerModel->save();
                Log::info("Updated player '{$playerModel->name}'", ['playerModel' => $playerModel]);
            } else {
                $playerModel = new Player();
                $playerModel['id'] = $player['id'];
                $playerModel['faction_id'] = $player['faction_id'];
                $playerModel['name'] = $player['name'];
                $playerModel->save();
                Log::info("Created player '{$playerModel->name}'", ['playerModel' => $playerModel]);
            }
            // Storing id's of records updated
            $updatedPlayerIds[] = $playerModel->id;

            // If it's a new player or the player was last updated awhile ago, then get new data!
            if ($playerModel->wasRecentlyCreated ||
                $playerModel->last_complete_update_at == null ||
                Carbon::parse($playerModel->last_complete_update_at)->diffInHours(Carbon::now()) >= 6) {
                Log::info("Dispatching full player update for '{$playerModel->name}'", ['playerModel' => $playerModel]);
                UpdatePlayer::dispatch($playerModel);
            }
        }

        //Delete any ID's that were not updated via the API
        Player::where('faction_id', $this->faction->id)->whereNotIn('id', $updatedPlayerIds)->delete();
    }
}
