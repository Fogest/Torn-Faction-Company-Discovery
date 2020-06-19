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
use Illuminate\Support\Collection;

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
        $playersInDatabase = $this->faction->players();

        $updatedPlayerIds = [];
        foreach ($this->players as $player) {
            $playerUpdate = Player::updateOrCreate(
                [
                    'id' => $player['id'],
                ],
                [
                    'faction_id' => $player['faction_id'],
                    'name' => $player['name'],
                ]
            );

            // Storing id's of records updated
            $updatedPlayerIds[] = $playerUpdate->id;
        }

        //Delete any ID's that were not updated via the API
        Player::where('faction_id', $this->faction->id)->whereNotIn('id', $updatedPlayerIds)->delete();
    }
}
