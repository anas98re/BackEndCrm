<?php

namespace App\Http\Controllers;

use App\Models\invoice_view;
use App\Http\Requests\Storeinvoice_viewRequest;
use App\Http\Requests\Updateinvoice_viewRequest;

class InvoiceViewController extends Controller
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
    public function store(Storeinvoice_viewRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoice_view $invoice_view)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoice_view $invoice_view)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateinvoice_viewRequest $request, invoice_view $invoice_view)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoice_view $invoice_view)
    {
        //
    }
}
