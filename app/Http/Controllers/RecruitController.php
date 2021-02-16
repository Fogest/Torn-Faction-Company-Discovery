<?php

namespace App\Http\Controllers;

use App\PlayerRecruit;
use Illuminate\Http\Request;

class RecruitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $recruits = PlayerRecruit::all();
//        dd($recruits[0]->recruiter->name);

        return view('recruit.index', compact('recruits'));
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'player_id' => 'required',
            'recruited_by_id' => 'required',
            'player_name' => 'required',
            'recruited_by' => 'required',
        ]);

        $recruit = PlayerRecruit::create($attributes);
        if ($recruit) {
            return response('Success', 200);
        } else {
            return response('Failure', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PlayerRecruit  $playerRecruit
     * @return \Illuminate\Http\Response
     */
    public function show(PlayerRecruit $playerRecruit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PlayerRecruit  $playerRecruit
     * @return \Illuminate\Http\Response
     */
    public function edit(PlayerRecruit $playerRecruit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlayerRecruit  $playerRecruit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlayerRecruit $playerRecruit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PlayerRecruit  $playerRecruit
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlayerRecruit $playerRecruit)
    {
        //
    }
}
