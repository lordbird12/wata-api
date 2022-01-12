<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(Request $request){
        return User::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>Hash::make($request->input('password'))
        ]);
    }

    public function login(Request $request){
        // if(!Auth::attempt($request->only('email','password'))){
        //     return response([
        //         'message' => 'Invalid credentials!'
        //     ],Response::HTTP_UNAUTHORIZED);
        // }

        // $user = Auth::User();
        
        // $token = $user->createToken('token')->plainTextToken;

        // $cookie = cookie('jwt',$token,60*24);

        // return response(['message'=>'succeess'])->withCookie($cookie);

        $creds = $request->only(['email','password']);

        $token = auth()->attempt($creds);

        return response()->json(['token'=>$token]);
    }

    public function user(){
        return 'Authenticated user';
    }
}
