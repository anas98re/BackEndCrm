<?php

namespace App\Http\Controllers;

use App\Models\config;
use App\Http\Requests\StoreconfigRequest;
use App\Http\Requests\UpdateconfigRequest;

class ConfigController extends Controller
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
    public function store(StoreconfigRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(config $config)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateconfigRequest $request, config $config)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(config $config)
    {
        //
    }
}
