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
     * @param  \App\Time  $time
     * @return \Illuminate\Http\Response
     */
    public function destroy(Time $time)
    {
        //
    }
}
