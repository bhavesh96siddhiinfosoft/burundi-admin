<?php

namespace App\Http\Controllers;


class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function globals()
    {
        return view("settings.global");
    }
    public function global()
    {
        return view("settings.global-setting");
    }
    public function privacyPolicy()
    {
        return view('settings.privacy_policy');
    }
    public function returnPolicy()
    {
        return view('settings.returns_policy');
    }
    public function termsAndConditions()
    {
        return view('settings.terms_conditions');
    }
    public function about()
    {
        return view('settings.aboutus');
    }
    public function languages()
    {
        return view('settings.languages.index');
    }

    public function saveLanguage($id = null)
    {
        return view('settings.languages.save')->with('id', $id);
    }

    public function contactUs()
    {
        
        return view('settings.contact_us');
    }
}
