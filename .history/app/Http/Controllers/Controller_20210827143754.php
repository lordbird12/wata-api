<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        auth()->setDefaultDriver('api');
    }

    public function returnSuccess($massage, $data)
    {

        return response()->json([
            'code' => strval(200),
            'status' => true,
            'message' => $massage,
            'data' => $data,
        ], 200);
    }

    public function returnUpdate($massage)
    {
        return response()->json([
            'code' => strval(201),
            'status' => true,
            'message' => $massage,
            'data' => [],
        ], 201);
    }

    public function returnErrorData($massage, $code)
    {
        return response()->json([
            'code' => strval($code),
            'status' => false,
            'message' => $massage,
            'data' => [],
        ], 404);
    }

    public function returnError($massage)
    {
        return response()->json([
            'code' => strval(401),
            'status' => false,
            'message' => $massage,
            'data' => [],
        ], 401);
    }

    public function uploadImage($image, $path)
    {
       
        $input['imagename'] = md5(rand(0, 999999) . $image->getClientOriginalName()) . '.' . $image->extension();
        $destinationPath = public_path('/thumbnail');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        $img = Image::make($image->path());
        $img->save($destinationPath . '/' . $input['imagename']);
        $destinationPath = public_path($path);
        $image->move($destinationPath, $input['imagename']);

        return $path . $input['imagename'];
    }


    public function getLastNumber($docId)
    {
        $doc = Doc::find($docId);

        if ($doc->gen) {

            //prefix
            if ($doc->prefix) {
                $Prefix = $doc->prefix;
            } else {
                $Prefix = '';
            }

            //date
            if ($doc->date) {

                if ($doc->date == 'YY') {
                    $Date = date('Y');
                } else if ($doc->date == 'YYMM') {
                    $Date = date('Ym');
                } else if ($doc->date == 'YYMMDD') {
                    $Date = date('Ymd');
                }
            } else {
                $Date = '';
            }

            //run number
            if ($doc->run_number) {

                $countPrefix = strlen($doc->prefix);
                $countRunNumber = strlen($doc->run_number);

                $lastDate = substr($doc->gen, $countPrefix, -$countRunNumber);

                //check date
                if ($Date > $lastDate) {

                    $lastNumber = 000;
                    $newNumber = intval($lastNumber) + 1;
                    $Number = sprintf('%0' . strval($countRunNumber) . 'd', $newNumber);

                } else {

                    $lastNumber = substr($doc->gen, -$countRunNumber);
                    $newNumber = intval($lastNumber) + 1;
                    $Number = sprintf('%0' . strval($countRunNumber) . 'd', $newNumber);

                }

            } else {
                $Number = null;
            }

        } else {

            //case new gen

            //prefix
            if ($doc->prefix) {
                $Prefix = $doc->prefix;
            } else {
                $Prefix = '';
            }

            //date
            if ($doc->date) {

                if ($doc->date == 'YY') {
                    $Date = date('Y');
                } else if ($doc->date == 'YYMM') {
                    $Date = date('Ym');
                } else if ($doc->date == 'YYMMDD') {
                    $Date = date('Ymd');
                }
            } else {
                $Date = '';
            }

            // dd($date);

            if ($doc->run_number) {
                $runNumber = intval($doc->run_number) + 1;
                $countZero = Strlen($doc->run_number);
                $Number = sprintf('%0' . strval($countZero) . 'd', $runNumber);
            } else {
                $Number = null;
            }

        }

        //format
        $prefix = $Prefix;
        $date = $Date;
        $run_number = $Number;

        //gen
        $gen = $prefix . $date . $run_number;

        return $gen;
    }

}
