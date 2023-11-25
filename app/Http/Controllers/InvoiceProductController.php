<?php

namespace App\Http\Controllers;

use App\Models\invoice_product;
use App\Http\Requests\Storeinvoice_productRequest;
use App\Http\Requests\Updateinvoice_productRequest;

class InvoiceProductController extends Controller
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
    public function store(Storeinvoice_productRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoice_product $invoice_product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoice_product $invoice_product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateinvoice_productRequest $request, invoice_product $invoice_product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoice_product $invoice_product)
    {
        //
    }
}
