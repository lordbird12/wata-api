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
      if (!isset($request->email)) {
        return $this->returnErrorData('[email] Data Not Found', 404);
    } else if (!isset($request->password)) {
        return $this->returnErrorData('[password] Data Not Found', 404);
    }

    $user = User::where('email', $request->email)
        ->where('password', md5($request->password))
        ->where('status', 'Yes')
        ->first();

    if ($user) {

        //log
        $username = $user->user_id;
        $log_type = 'Login';
        $log_description = 'User ' . $username . ' has ' . $log_type;
        $this->Log($username, $log_description, $log_type);
        //

        return response()->json([
            'code' => '200',
            'status' => true,
            'message' => 'Login successfully',
            'data' => $user,
            'token' => $this->genToken($user->id, $user),
        ], 200);
    } else {
        return $this->returnError('Incorrect Email or Password', 401);
    }
    }

    protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ]);
  }

    public function user(){
        return 'Authenticated user';
    }
}
