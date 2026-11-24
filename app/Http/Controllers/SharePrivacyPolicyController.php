<?php

namespace App\Http\Controllers;


class SharePrivacyPolicyController extends Controller
{

    public function __construct()
    {
       
    }

    public function share()
    {
        return view("privacy_policy");
    }
    public function shareContactUS()
    {
        return view("contact_us");
    }
    
   

}
