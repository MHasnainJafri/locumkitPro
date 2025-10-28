<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function NotificationController(Request $request){
        dd($request->all(), 'here in request');
    }
}
