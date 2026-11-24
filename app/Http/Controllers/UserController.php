<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

     public function __construct()
    {
       $this->middleware('auth'); 
    }

  public function profile()
  {
      $user = Auth::user();
   
      return view('users.profile', compact(['user']));
  }

  public function update(Request $request,$id){

    $name = $request->input('name');
    $password = $request->input('password');
    $old_password = $request->input('old_password');
    $email = $request->input('email');
    
    if($password == ''){
        $validator = Validator::make($request->all(), [
    		'name' => 'required|max:255',
            'email'=>'required|email'
		]);   
    }else{
        $user = Auth::user();
        if(password_verify($old_password,$user->password)){
          $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'email'=>'required|email'
          ]);

        }else{
            return Redirect()->back()->with(['message' => "Please enter correct old password"]);
        }
     
    }

    if ($validator->fails()) {
      $error = $validator->errors()->first();
      return Redirect()->back()->with(['message' => $error]);
    } 

    $user = User::find($id);
    if($user) {
      $user->name = $name;
      $user->email = $email;
      if($password != ''){
          $user->password = Hash::make($password);
      }
      $user->save();
    }

    return redirect()->back();
  }

  public function showMismatchedQuotaUsers(Request $request)
  {
    $ids = $request->get('ids', []);
      return view('users.revertedQuotaUsers', compact('ids'));
  }
  public function state()
  {
      return view('cityState.state.index');
  }
  public function stateSave($id = null)
  {
      return view('cityState.state.save')->with('id', $id);
  }
 

}
