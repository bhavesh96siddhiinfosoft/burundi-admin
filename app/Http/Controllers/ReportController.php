<?php
namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('reports.index');
    }
    public function transcationReport()
    {
        return view("reports.transaction-report");
    }
    public function customerReport()
    {
        return view("reports.customer-report");
    }
    public function ownerReport()
    {
        return view("reports.owner-report");
    }
    public function ownerTransactionReport()
    {
        return view("reports.owner-transaction");
    }



}
