<?php

namespace App\Http\Controllers;

use App\Models\clients;
use App\Http\Requests\StoreclientsRequest;
use App\Http\Requests\UpdateclientsRequest;

class ClientsController extends Controller
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
    public function store(StoreclientsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(clients $clients)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(clients $clients)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateclientsRequest $request, clients $clients)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(clients $clients)
    {
        //
    }
}
