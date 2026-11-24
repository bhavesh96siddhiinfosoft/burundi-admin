<?php
namespace App\Http\Controllers;

class OwnersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('owners.index');
    }
    public function create()
    {
        return view("owners.create");
    }
  
    public function edit($id)
    {
        return view("owners.edit",compact("id"));
    }

    public function view($id)
    {
        return view("owners.view",compact("id"));
    }
   public function deletedOwner(){
        return view('owners.deleted-owner');
   }
}
