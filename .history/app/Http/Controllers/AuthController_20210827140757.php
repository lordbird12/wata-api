<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

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

    public function checkLogin(Request $request)
    {
        $header = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $header);

        try {

            if ($token == "") {
                return $this->returnError('Token Not Found', 401);
            }

            $payload = JWT::decode($token, $this->key, array('HS256'));
            $payload->exp = Carbon::now()->timestamp + 86400;
            $token = JWT::encode($payload, $this->key);

            return response()->json([
                'code' => '200',
                'status' => true,
                'message' => 'Active',
                'data' => [],
                'token' => $token,
            ], 200);
        } catch (\Firebase\JWT\ExpiredException $e) {

            list($header, $payload, $signature) = explode(".", $token);
            $payload = json_decode(base64_decode($payload));
            $payload->exp = Carbon::now()->timestamp + 86400;
            $token = JWT::encode($payload, $this->key);

            return response()->json([
                'code' => '200',
                'status' => true,
                'message' => 'Token is expire',
                'data' => [],
                'token' => $token,
            ], 200);

        } catch (Exception $e) {
            return $this->returnError('Can not verify identity', 401);
        }
    }

    public function register(Request $request){
        // $user = User::create([
        //     'name'=>$request->input('name'),
        //     'email'=>$request->input('email'),
        //     'password'=>Hash::make($request->input('password'))
        // ]);

        if($request->hasfile('image'))
         {
             dd($request->image);
            foreach($request->image as $file)
            {

                dd($file->getClientOriginalName());
                // $image = new Image;
                // $name = $file->getClientOriginalName();
                // $image->move(public_path().'/images/',$name);
                // $post->photos()->save($image);
            }
         }

        // dd($request->hasfile('image'));

        DB::beginTransaction();

        try {

            $User = new User();
            $User->user_id = '1';
            $User->fname = $request->fname;
            $User->lname = $request->lname;
            $User->email = $request->email;
            $User->password = Hash::make($request->input('password'));
            $User->image = $this->uploadImage($request->image, '/images/users/');
            $User->age = $request->age;
            $User->sex = $request->sex;
            $User->phone = $request->phone;
            $User->line = $request->line;
            $User->type = $request->type;

            $User->save();

            DB::commit();

            return $this->returnSuccess('Successful operation', []);
        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData('Something went wrong Please try again ' . $e, 404);
        }

        return $this->login($request);
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
