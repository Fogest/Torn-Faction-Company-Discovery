<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Player $player
     * @return Response
     */
    public function show(Player $player)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Player $player
     * @return Response
     */
    public function edit(Player $player)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Player  $player
     * @return Response
     */
    public function update(Request $request, Player $player)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Player $player
     * @return Response
     */
    public function destroy(Player $player)
    {
        //
    }

    public function addApiKey(Request $request)
    {
        $apiKey = $request->input('api_key');

        $response = Http::withOptions(
            [
                'verify' => false,
                'base_uri' => config('custom.torn_api_base'),
                'timeout' => 5.0
            ]
        )->get(
            "user/",
            [
                'selections' => 'profile',
                'key' => $apiKey
            ]
        );
        if ($response->failed() || $response->serverError()) {
            return 'API Key invalid or Torn API is down';
        }
        $tornPlayerData = $response->json();
        $playerId = $tornPlayerData['player_id'];

        $player = Player::find($playerId);
        if (!$player) {
            return 'Player not in a Nuclear faction';
        }
        $player->api_key = $apiKey;
        $player->save();
        $request->session()->put('player.api_key', $player->api_key);
        $request->session()->put('player.id', $player->id);
        return 'Successfully saved API key to ' . $player->name . ' [' . $player->id . ']';
    }
}
