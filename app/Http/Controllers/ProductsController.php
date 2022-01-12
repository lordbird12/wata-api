<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\ProductFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Products::all();
        if ($products) {
            foreach ($products as &$value) {
                $value['main_picture'] =  url($value['main_picture']);
            }
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างข่าวสารสำเร็จ',
                'data' => $products,
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
                'user_id' => 'required',
                'title' => 'required',
                'shot_detail' => 'required',
                'detail' => 'required',
                'price' => 'required',
                'discount' => 'required',
            ],
            [
                'user_id.required' => 'กรุณาระบุรหัสผู้สร้าง',
                'title.required' => 'กรุณาระบุชื่อหัวข่าว',
                'shot_detail.required' => 'กรุณาระบุเนื้อหาข่าวย่อ',
                'detail.required' => 'กรุณาระบุเนื้อหาข่าวเต็ม',
                'price.required' => 'กรุณาใส่ราคาขาย',
                'discount.required' => 'กรุณาใส่ราคาส่วนลด',
            ]
        );

        if(!$request->hasFile('main_picture')) {
            return response()->json(['upload_file_not_found main_picture'], 400);
        }

        $user_id = $request->input('user_id');
        $title = $request->input('title');
        $shot_detail = $request->input('shot_detail');
        $detail = $request->input('detail');
        $main_picture = $request->file('main_picture');
        $price = $request->input('price');
        $discount = $request->input('discount');

        DB::beginTransaction();
        try {
            $product = new Products();
            $product->user_id = $user_id;
            $product->title = $title;
            $product->shot_detail = $shot_detail;
            $product->detail = $detail;
            $product->main_picture = $this->uploadImage($main_picture, 'images/products/');
            $product->price = $price;
            $product->discount = $discount;
            $product->save();


            if ($validator->fails()) {
                $errors = $validator->errors()->first();
                return $this->returnError($errors, 400);
            }
    
            if(!$request->hasFile('images')) {
                return response()->json(['upload_file_not_found'], 400);
            }
    
            $allowedfileExtension=['jpg','png'];
            $files = $request->file('images'); 
            $errors = [];

            foreach ($files as $file) {      
    
                if($file->isValid()){
                $extension = $file->getClientOriginalExtension();
         
                $check = in_array($extension,$allowedfileExtension);
         
                if($check) {
                        $path = $file->store('public/products');
                        $name = $file->getClientOriginalName();
            
                        $Files = new ProductFiles();
                        $Files->product_id = $product->id;
                        $Files->title = $this->uploadImage($file, '/products/');
                        $Files->path = $path;
                        $Files->save();
                    }
                } 
            }

            DB::commit();
            $product->main_picture = url($product->main_picture);
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสร้างสำเร็จ',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Products::find($id);
        if ($product) {
            $productFiles = ProductFiles::where('product_id', '=', $product->id)->get();
            
            foreach ($productFiles as &$value) {
                $value['image'] =  url($value['title']);
            }

            $product->main_picture = url($product->main_picture);
            $product->path = $productFiles;
            return response()->json([
                'code' => '200',
                'status' => true,
                'massage' => 'ดำเนินการสำเร็จ',
                'data' => $product,
            ], 200);
        } else {
            return response()->json(['massage' => 'ไม่พบข้อมูลข่าวสาร'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $products)
    {
        $title = $request->input('title');
        $shot_detail = $request->input('shot_detail');
        $detail = $request->input('detail');
        $image = $request->file('image');

        $new = Products::find($products);

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
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy($products)
    {
        $product = Products::find($products);
        if ($product) {
            $product->delete();
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

        $u = Products::select($col)
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
