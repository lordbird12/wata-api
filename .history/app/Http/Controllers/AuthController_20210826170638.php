<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login', 'register']]);
  }

    public function register(Request $request){
        $user = User::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>Hash::make($request->input('password'))
        ]);

        $token = auth()->login($user);

        return $this->respondWithToken($token);
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

        // $credentials = request(['email', 'password']);
        //return response()->json($credentials);
        if (!$token = auth()->attempt($request->only('email','password'))) {
          return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function user(){
        return 'Authenticated user';
    }
}
