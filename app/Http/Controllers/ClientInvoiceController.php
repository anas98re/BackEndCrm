<?php

namespace App\Http\Controllers;

use App\Models\client_invoice;
use App\Http\Requests\Storeclient_invoiceRequest;
use App\Http\Requests\Updateclient_invoiceRequest;

class ClientInvoiceController extends Controller
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
    public function store(Storeclient_invoiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(client_invoice $client_invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(client_invoice $client_invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateclient_invoiceRequest $request, client_invoice $client_invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(client_invoice $client_invoice)
    {
        //
    }
}
