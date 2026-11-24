<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth');
    }


	public function distributorWalletTransaction()
    {
        return view("transaction.distributor_transaction");
    }
    // this is used for client 
    public function clientWalletTransaction()
    {
        return view("transaction.client_transaction");
    }
}
