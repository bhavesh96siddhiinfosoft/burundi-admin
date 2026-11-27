<?php
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App;
  
class FuelPurchaseManageController extends Controller
{
 
    public function fuelPurchasesListing()
    { 
        return view('fuel_purchase.index'); 
    }
    public function fuelPurchasesSave($id = '')
    { 
        return view('fuel_purchase.save', compact('id')); 
    }

    public function fuelReceivingsListing()
    { 
        return view('fuel_receive.index'); 
    }
    public function fuelReceivingsSave($id = '')
    { 
        return view('fuel_receive.save', compact('id')); 
    }

    public function fuelTaxVerificationsListing()
    { 
        return view('fuel_tax_verification.index'); 
    }
    public function fuelTaxVerificationsSave($id = '')
    { 
        return view('fuel_tax_verification.save', compact('id')); 
    }

    public function fuelRequestListing()
    { 
        return view('fuel_request.index'); 
    }
    public function fuelRequestSave($id = '')
    {
        return view('fuel_request.save', compact('id'));
    }
    public function fuelRequestReceive($id)
    {
        return view('fuel_request.receive', compact('id'));
    }
}