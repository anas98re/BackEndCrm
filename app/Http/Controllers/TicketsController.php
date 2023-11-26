<?php

namespace App\Http\Controllers;

use App\Models\tickets;
use App\Http\Requests\StoreticketsRequest;
use App\Http\Requests\UpdateticketsRequest;

class TicketsController extends Controller
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
    public function store(StoreticketsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(tickets $tickets)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tickets $tickets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateticketsRequest $request, tickets $tickets)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tickets $tickets)
    {
        //
    }
}
