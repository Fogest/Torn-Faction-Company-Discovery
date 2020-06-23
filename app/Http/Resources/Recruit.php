<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Recruit extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'player_id' => $this->player_id,
            'faction_id' => $this->faction_id,
            'recruited_by_id' => $this->recruited_by_id,
            'player_name' => $this->player_name,
            'recruited_by' => $this->recruited_by,
            'faction_name' => $this->faction_name,
            'is_required_stats' => $this->is_required_stats,
            'is_accepted' => $this->is_accepted,
            'created_at' => $this->created_at->addHours(4)->format('Y.m.d H:i:s'),
            'updated_at' => $this->updated_at->addHours(4)->format('Y.m.d H:i:s'),
        ];
    }
}
