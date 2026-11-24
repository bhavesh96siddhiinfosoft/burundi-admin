<?php

namespace App\Http\Controllers;


class AdminFuelQuotaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('fuel_quota.index');
    }
    public function edit($id)
    {
        return view('fuel_quota.edit')->with('id', $id);
    }

    public function create()
    {
        return view('fuel_quota.create');
    }
    public function history()
    {
        return view('fuel_quota.history');
    }

}