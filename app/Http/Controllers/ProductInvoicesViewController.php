<?php

namespace App\Http\Controllers;

use App\Models\product_invoices_view;
use App\Http\Requests\Storeproduct_invoices_viewRequest;
use App\Http\Requests\Updateproduct_invoices_viewRequest;

class ProductInvoicesViewController extends Controller
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
    public function store(Storeproduct_invoices_viewRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(product_invoices_view $product_invoices_view)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product_invoices_view $product_invoices_view)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updateproduct_invoices_viewRequest $request, product_invoices_view $product_invoices_view)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(product_invoices_view $product_invoices_view)
    {
        //
    }
}
