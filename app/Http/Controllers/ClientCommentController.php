<?php

namespace App\Http\Controllers;

use App\Models\client_comment;
use App\Http\Requests\Storeclient_commentRequest;
use App\Http\Requests\Updateclient_commentRequest;

class ClientCommentController extends Controller
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
    public function store(Storeclient_commentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(client_comment $client_comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(client_comment $client_comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateclient_commentRequest $request, client_comment $client_comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(client_comment $client_comment)
    {
        //
    }
}
