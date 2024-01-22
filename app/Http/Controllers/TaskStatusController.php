<?php

namespace App\Http\Controllers;

use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;

class TaskStatusController extends Controller
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
    public function store(StoretaskStatusRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(taskStatus $taskStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(taskStatus $taskStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetaskStatusRequest $request, taskStatus $taskStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(taskStatus $taskStatus)
    {
        //
    }
}
