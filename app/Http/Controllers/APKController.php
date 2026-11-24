<?php

namespace App\Http\Controllers;


class APKController extends Controller
{

    public function download($apk)
    {
        
        if ($apk == 'employee') {
            $filename = 'employee.apk';
        } else if ($apk == 'owner') {
            $filename = 'owner.apk';
        } else if ($apk == 'customer') {
            $filename = 'customer.apk';
        }else{
            $filename = '';
        }
        $filePath = storage_path('app/apk/' . $filename);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404);
        }
    }


}