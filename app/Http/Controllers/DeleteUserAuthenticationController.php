<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Auth;
use Illuminate\Http\Request;
class DeleteUserAuthenticationController extends Controller
{

    protected $auth;

    public function __construct()
    {
        $this->auth = app('firebase.auth');
    }

    public function deleteUser($uid)
    {
        try {
            $this->auth->deleteUser($uid);
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateEmail($uid,Request $request)
    {
        $newEmail = $request->input('email');
        try {
            $this->auth->updateUser($uid, ['email' => $newEmail]);
            return response()->json(['message' => 'Email updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}