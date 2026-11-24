<?php
namespace App\Http\Controllers;

class VehicleDaysController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('vehicle_days.index');
    }
    public function create()
    {
        return view("vehicle_days.create");
    }

    public function edit($id)
    {
        return view("vehicle_days.edit", compact("id"));
    }



}
