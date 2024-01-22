<?php

namespace App\Http\Controllers;

use App\Models\agent;
use App\Http\Requests\StoreagentRequest;
use App\Http\Requests\UpdateagentRequest;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreagentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateagentRequest $request, agent $agent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(agent $agent)
    {
        //
    }
}
