<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class BannersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banners::all();
        if ($banners) {
            // $new->image = $new->image;

            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างสำเร็จ',
                'data' => $banners,
            ], 200);
        } else {
            return response()->json(['massage' => 'ไม่พบข้อมูล'], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required',
            ],
            [
                'title.required' => 'กรุณาระบุชื่อ',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnError($errors, 400);
        }

        $title = $request->input('title');

        DB::beginTransaction();

        try {
            $banner = new Banners();
            $banner->title = $title;
            $banner->imagePath = $this->uploadImage($request->file('image'), 'images/banners/');
            $banner->priority = 0;
            $banner->active = 1;
            $banner->save();
            DB::commit();

            $banner->imagePath = url($banner->imagePath);
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างสำเร็จ',
                'data' => $banner,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "ดำเนินการสร้างล้มเหลว"], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banners  $banner
     * @return \Illuminate\Http\Response
     */
    public function show($banner)
    {
        $banner = Banners::find($banner);
        if ($banner) {
            $banner->image = url($banner->image);

            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างสำเร็จ',
                'data' => $banner,
            ], 200);
        } else {
            return response()->json(['massage' => 'ไม่พบข้อมูล'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Banners  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banners $banner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banners  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $banner)
    {
        $title = $request->input('title');
        $shot_detail = $request->input('shot_detail');
        $detail = $request->input('detail');
        $image = $request->file('image');

        $banner = Banners::find($banner);

        DB::beginTransaction();
        try {
            if (isset($title))
                $banner->title = $title;
            if (isset($shot_detail))
                $banner->shot_detail = $shot_detail;
            if (isset($detail))
                $banner->detail = $detail;
            if ($request->hasFile('image'))
                $banner->image = $this->uploadImage($image, 'images/banners/');

            $banner->save();
            DB::commit();
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการแก้ไขสำเร็จ',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "ดำเนินการแก้ไขล้มเหลว"], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Banners  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy($banner)
    {
        $banner = Banners::find($banner);
        if ($banner) {
            $banner->delete();
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการลบสำเร็จ',
                'data' => [],
            ], 204);
        } else {
            return response()->json(["message" => "ดำเนินการลบล้มเหลว"], 400);
        }
    }

    public function table(Request $request)
    {

        $columns = $request->input('columns');
        $length = $request->input('length');
        $order = $request->input('order');
        $search = $request->input('search');
        $start = $request->input('start');
        $page = $start / $length + 1;

        $col = array('id', 'title', 'shot_detail', 'detail', 'created_at');

        $u = Banners::select($col)
            ->orderby($col[$order[0]['column']], $order[0]['dir']);

        if ($search['value'] != '' && $search['value'] != null) {
            foreach ($col as &$c) {
                $u->orWhere($c, 'LIKE', '%' . $search['value'] . '%');
            }
        }

        $member = $u->paginate($length, $col, 'page', $page);

        return response()->json($member, 200);
    }
}
