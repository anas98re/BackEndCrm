<?php

namespace App\Http\Controllers;

use App\Models\user_token;
use App\Http\Requests\Storeuser_tokenRequest;
use App\Http\Requests\Updateuser_tokenRequest;

class UserTokenController extends Controller
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
    public function store(Storeuser_tokenRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(user_token $user_token)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user_token $user_token)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateuser_tokenRequest $request, user_token $user_token)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user_token $user_token)
    {
        //
    }
}
