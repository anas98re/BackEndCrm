<?php

namespace App\Http\Controllers;

use App\Models\config_table;
use App\Http\Requests\Storeconfig_tableRequest;
use App\Http\Requests\Updateconfig_tableRequest;

class ConfigTableController extends Controller
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
    public function store(Storeconfig_tableRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(config_table $config_table)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(config_table $config_table)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateconfig_tableRequest $request, config_table $config_table)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(config_table $config_table)
    {
        //
    }
}
