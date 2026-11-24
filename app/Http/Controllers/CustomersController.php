<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CustomersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        return view("customers.index2");
    }
    public function assignPetrolPumpToCustomer(Request $request)
    {

        return view("customers.assignPetrolPump");
    }
    public function deletedCustomer(Request $request)
    {

        return view("customers.deleted-customer");
    }

    public function create()
    {
        return view("customers.create");
    }

    public function edit($id)
    {
        return view("customers.edit")->with('id',$id);
    }

    public function odometerReadingHistory($id)
    {
        return view("customers.odometerReadingHistory.index")->with('id',$id);
    }

    public function odometerReadingHistoryView($id)
    {
        return view("customers.odometerReadingHistory.view")->with('id',$id);
    }


    public function view($id){
        return view('customers.view',compact('id'));
    }
     public function history()
    {
        return view("customers.fuel_reset_history");
    }

}
