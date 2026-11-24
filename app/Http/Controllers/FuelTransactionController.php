<?php
namespace App\Http\Controllers;

class FuelTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('fuel_transaction.index');
    }
    public function view($id)
    {
        return view("fuel_transaction.view")->with('id',$id);
    }

}
