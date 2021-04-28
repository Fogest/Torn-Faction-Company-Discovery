<?php

namespace App\Http\Controllers;

use App\Player;
use App\Time;
use Illuminate\Http\Request;

class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $times = [];
        $playerId = session('player.id', null);
        if (is_null($playerId)) {
            return view('time.index', compact('times'));
        }

        $player =  Player::find($playerId);
        if (!$player) {
            return view('time.index', compact('times'));
        }

        $times = $player->times;
        return view('time.index', compact('times'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $time = Time::create([
            'player_id' => $request->session()->get('player.id'),
            'event_id' => $request->event_id,
            'event_name' => $request->event_name,
            'recurring' => $request->recurring,
            'multiple_per_day' => $request->multiple_per_day,
            'day_of_week' => $request->day_of_week,
            'event_date_time' => $request->event_date_time,
        ]);

        return 'Time Saved to Database';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function show(Time $time)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function edit(Time $time)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Time $time)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return string
     */
    public function destroy(Request $request)
    {
        $playerId = session('player.id', null);
        if (is_null($playerId)) {
            return 'Unable to locate player session, please re-save API key';
        }

        $player =  Player::find($playerId);
        if (!$player) {
            return 'Unable to locate player session, please re-save API key';
        }

        $eventId = $request->event_id;
        if (is_null($eventId)) {
            return 'No event ID was provided';
        }

        $time = $player->times()->where('event_id', $eventId)->first();
        if (!$time) {
            return 'Unable to locate a custom event with this ID';
        }

        $time->delete();
        return 'Event Deleted';
    }

    /**
     * Destroy all of custom times for a specified user.
     *
     * @param Request $request
     * @return string
     */
    public function destroyAll(Request $request)
    {
        $times = [];
        $playerId = session('player.id', null);
        if (is_null($playerId)) {
            return 'Unable to locate player session, please re-save API key';
        }

        $player =  Player::find($playerId);
        if (!$player) {
            return 'Unable to locate player session, please re-save API key';
        }

        $times = $player->times()->delete();
        $player->api_key = null;
        $player->save();
        $request->session()->forget('player.api_key');
        return 'API key and custom event data removed';
    }
}
