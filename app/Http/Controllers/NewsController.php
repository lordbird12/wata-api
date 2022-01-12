<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::all();
        if ($news) {

            foreach ($news as &$value) {
                $value['image'] =  url($value['image']);
            }

            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างข่าวสารสำเร็จ',
                'data' => $news,
            ], 200);
        } else {
            return response()->json(['massage' => 'ไม่พบข้อมูลข่าวสาร'], 404);
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
                'shot_detail' => 'required',
                'detail' => 'required',
                'image' => 'required',
            ],
            [
                'title.required' => 'กรุณาระบุชื่อหัวข่าว',
                'shot_detail.required' => 'กรุณาระบุเนื้อหาข่าวย่อ',
                'detail.required' => 'กรุณาระบุเนื้อหาข่าวเต็ม',
                'image.required' => 'กรุณาใส่รูปภาพ',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return $this->returnError($errors, 400);
        }

        $title = $request->input('title');
        $shot_detail = $request->input('shot_detail');
        $detail = $request->input('detail');
        $image = $request->file('image');

        DB::beginTransaction();
        try {
            $news = new News();
            $news->title = $title;
            $news->shot_detail = $shot_detail;
            $news->detail = $detail;
            $news->image = $this->uploadImage($image, 'images/news/');
            $news->save();

            DB::commit();
            $news->image = url($news->image);
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างข่าวสารสำเร็จ',
                'data' => $news,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "ดำเนินการสร้างข่าวสารล้มเหลว"], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function show($news)
    {
        $new = News::find($news);
        if ($new) {
            $new->image = url($new->image);

            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างข่าวสารสำเร็จ',
                'data' => $new,
            ], 200);
        } else {
            return response()->json(['massage' => 'ไม่พบข้อมูลข่าวสาร'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $news)
    {
        $title = $request->input('title');
        $shot_detail = $request->input('shot_detail');
        $detail = $request->input('detail');
        $image = $request->file('image');

        $new = News::find($news);

        DB::beginTransaction();
        try {
            if (isset($title))
                $new->title = $title;
            if (isset($shot_detail))
                $new->shot_detail = $shot_detail;
            if (isset($detail))
                $new->detail = $detail;
            if ($request->hasFile('image'))
                $new->image = $this->uploadImage($image, 'images/news/');

            $new->save();
            DB::commit();
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการแก้ไขข่าวสารสำเร็จ',
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "ดำเนินการแก้ไขข่าวสารล้มเหลว"], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News  $news
     * @return \Illuminate\Http\Response
     */
    public function destroy($news)
    {
        $new = News::find($news);
        if ($new) {
            $new->delete();
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการลบข่าวสารสำเร็จ',
                'data' => [],
            ], 204);
        } else {
            return response()->json(["message" => "ดำเนินการลบข่าวสารล้มเหลว"], 400);
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

        $u = News::select($col)
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
