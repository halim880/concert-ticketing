<?php

namespace App\Http\Controllers;

use App\Models\Consert;
use Illuminate\Http\Request;

class ConsertsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Consert  $consert
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $consert = Consert::whereNotNull('published_at')->findOrFail($id);
        return view('consert.show', compact('consert'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Consert  $consert
     * @return \Illuminate\Http\Response
     */
    public function edit(Consert $consert)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consert  $consert
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consert $consert)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Consert  $consert
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consert $consert)
    {
        //
    }
}
