<?php

namespace App\Http\Controllers;

use App\Faction;
use App\Jobs\UpdateAllFactionData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        UpdateAllFactionData::dispatchNow();
        return redirect('');
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
     * @param  Faction $faction
     * @return Response
     */
    public function show(Faction $faction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Faction $faction
     * @return Response
     */
    public function edit(Faction $faction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Faction $faction
     * @return Response
     */
    public function update(Request $request, Faction $faction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Faction $faction
     * @return Response
     */
    public function destroy(Faction $faction)
    {
        //
    }
}
