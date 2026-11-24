<?php
namespace App\Http\Controllers;

class OnBoardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('onboard.index');
    }
    public function edit($id)
    {
        return view("onboard.edit")->with('id',$id);
    }

}
