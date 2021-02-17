<?php

namespace App\Jobs\Recruiting;

use App\Faction;
use App\PlayerRecruit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateRecruitPlayer implements ShouldQueue
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
     * @param $recruit
     */
    public function __construct(PlayerRecruit $recruit)
    {
        // Recruit only has the recruiting Player() relation associated with it,
        // thus we do not need to get the relationship with it as it makes the
        // serialization a larger size. We don't need it, so may as well cut it out.
        $this->recruit = $recruit->withoutRelations();
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
            "user/" . $this->recruit->player_id,
            [
                'selections' => 'profile',
                'key' => config('custom.torn_api_key')
            ]
        );
        $tornPlayerData = $response->json();

        Log::info(
            "Updating recruit player '{$this->recruit->name}' now",
            ['recruit' => $this->recruit, 'recruitData' => $tornPlayerData]
        );

        // Faction ID is 0 when they are in no faction.
        if (isset($tornPlayerData['faction'])) {
            if ((int) $tornPlayerData['faction']['faction_id'] != 0) {
                $this->recruit['faction_id'] = $tornPlayerData['faction']['faction_id'];
                $this->recruit['faction_name'] = $tornPlayerData['faction']['faction_name'];
            } else {
                $this->recruit['faction_id'] = null;
                $this->recruit['faction_name'] = null;
            }

            // Check if the faction id is in the factions table (this table only
            // contains Nuke factions, thus implies they are recruited!)
            // If they are already marked as "accepted" don't mess with it.
            $faction = Faction::where('id', $this->recruit->faction_id)->first();
            if ($faction && $this->recruit->is_accepted != true) {
                $this->recruit['is_accepted'] = true;
            }
            if ($this->recruit->isDirty()) {
                $this->recruit->save();
            } else {
                $this->recruit->touch();
            }
        }
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
