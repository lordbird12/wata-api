<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $orders = $request->orders;
        $loginBy = $request->login_by;

        if (empty($orders)) {
            return $this->returnErrorData('[orders] Data Not Found', 404);
        } else if (!isset($loginBy)) {
            return $this->returnErrorData('[login_by] Data Not Found', 404);
        }

        DB::beginTransaction();
        $orderNo = $this->getLastNumberOrders();
        try {
            for ($i = 0; $i < count($orders); $i++) {

                $order[$i]['order_no'] = $orderNo;
                $order[$i]['user_id'] = $loginBy->user_id;
                $order[$i]['product_id'] = $orders[$i]['product_id'];
                $order[$i]['qty'] = $orders[$i]['qty'];
                $order[$i]['price'] = $orders[$i]['price'];
                $order[$i]['discount'] = $orders[$i]['discount'];
                $order[$i]['order_status'] = 'W';
                $order[$i]['created_at'] = Carbon::now()->toDateTimeString();
                $order[$i]['updated_at'] = Carbon::now()->toDateTimeString();

            }
            // dd($order);

            DB::table('orders')->insert($order);

            DB::commit();

            return $this->returnSuccess('Successful operation', ['Order No.' => $orderNo]);

        } catch (\Throwable $e) {

            DB::rollback();

            return $this->returnErrorData($e, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function show(Orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orders  $orders
     * @return \Illuminate\Http\Response
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
