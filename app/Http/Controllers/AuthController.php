<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Files;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

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
        if(!$request->hasFile('files')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $userId = $this->getLastNumber();
        $allowedfileExtension=['pdf','jpg','png'];
        $files = $request->file('files'); 
        $errors = [];
     
        foreach ($files as $file) {      

            if($file->isValid()){
            $extension = $file->getClientOriginalExtension();
     
            $check = in_array($extension,$allowedfileExtension);
     
            if($check) {
                    $path = $file->store('public/files');
                    $name = $file->getClientOriginalName();
                    
                    $Files = new Files();
                    $Files->user_id = $userId;
                    $Files->title = $this->uploadImage($file, '/files/');
                    $Files->path = $path;
                    $Files->save();
                }
            } 
        }
       
        DB::beginTransaction();

        try {

            $User = new User();
            $User->user_id = $userId;
            $User->fname = $request->fname;
            $User->lname = $request->lname;
            $User->email = $request->email;
            $User->password = md5($request->password);
            $User->image = $this->uploadImage($request->file('image'), '/images/users/');
            $User->birthday = $request->birthday;
            $User->age = $request->age;
            $User->sex = $request->sex;
            $User->phone = $request->phone;
            $User->line = $request->line;
            $User->position = $request->position;
            $User->department = $request->department;
            $User->organization = $request->organization;
            $User->type = $request->type;

            $User->save();

            DB::commit();

            return $this->login($request);
        } catch (Exception $e) {
            dd($e);

            DB::rollback();

            return $this->returnErrorData($e, 404);
        }

        
    }

    public function login(Request $request){
        
      if (!isset($request->email)) {
        return $this->returnErrorData('[email] Data Not Found', 404);
      } else if (!isset($request->password)) {
          return $this->returnErrorData('[password] Data Not Found', 404);
      }

      $user = User::where('email', $request->email)
          ->where('password', md5($request->password))
          ->first();


      if ($user) {
          $user->image = url($user->image);
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
