<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request){
        
        $creds = $request->only(['email','password']);

        $token = auth()->attempt($creds);

        return $token;
    }
}
