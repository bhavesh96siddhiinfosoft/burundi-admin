<?php
namespace App\Http\Controllers;

class DistributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('distributions.index');
    }

    public function create()
    {
        return view('distributions.create');
    }

    public function view($id)
    {
        return view('distributions.view')->with('id', $id);
    }
}
