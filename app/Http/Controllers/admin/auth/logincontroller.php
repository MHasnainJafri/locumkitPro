<?php

namespace App\Http\Controllers\admin\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class logincontroller extends Controller
{
    public function showLoginForm(){
        return view('admin.auth.login');
    }
    public function login(Request $request){
dd($request);
    }
}
