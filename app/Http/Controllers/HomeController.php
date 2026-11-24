<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	return view('home');
        
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        return view('dashboard');
    }    
    
    public function users()
    {
        return view('users');
    }

      public function saveLogActivity(Request $request)
    {
        \App\Models\Activity::create([
            'user_id' => $request->user_id, 
            'action' => $request->action,
            'details' => json_encode($request->details),
            'created_at' => Carbon::now(),
            'source' => 'firebase'
        ]);
         return response()->json(['status' => 'log updated successfully.']);
    }
    
}
