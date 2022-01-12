<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use \Firebase\JWT\JWT;

class AuthController extends Controller
{

    public $key = "wmta_key";

    public function genToken($id, $name)
    {
        $payload = array(
            "iss" => "wmta",
            "aud" => $id,
            "lun" => $name,
            "iat" => Carbon::now()->timestamp,
            "exp" => Carbon::now()->timestamp + 86400,
            "nbf" => Carbon::now()->timestamp,
        );

        $token = JWT::encode($payload, $this->key);
        return $token;
    }

    public function register(Request $request){
        $user = User::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>Hash::make($request->input('password'))
        ]);

        $this->login($user);

    }

    public function login(Request $request){
      if (!isset($request->email)) {
        return $this->returnErrorData('[email] Data Not Found', 404);
    } else if (!isset($request->password)) {
        return $this->returnErrorData('[password] Data Not Found', 404);
    }

    $user = User::where('email', $request->email)
        // ->where('password', Hash::make($request->password))
        ->first();

        // dd($user);

    if ($user) {

        //log
        $username = $user->id;
        $log_type = 'Login';
        $log_description = 'User ' . $username . ' has ' . $log_type;
        // $this->Log($username, $log_description, $log_type);
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
