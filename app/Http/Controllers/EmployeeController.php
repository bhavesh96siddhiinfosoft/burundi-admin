<?php
namespace App\Http\Controllers;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('employee.index');
    }
    public function create()
    {
        return view("employee.create");
    }

    public function edit($id)
    {
        return view("employee.edit", compact("id"));
    }



}
