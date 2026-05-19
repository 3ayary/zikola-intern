<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::select('id', 'user_id', 'total_price')->paginate($this->pagination); 

        return ApiResponse::success($orders, 'get orders successfully', 200);  //data.data
    }

    public function store(OrderRequest $req)
    {
        $totalPrice = 0;
        $attachData = [];

        foreach ($req->products as $item) {
            $product = Product::findOrFail($item['product_id']);
            $attachData[$product->id] = ['quantity' => $item['quantity']];
            $totalPrice += $product->price * $item['quantity'];
        }

        $order = DB::transaction(function () use ($req, $totalPrice, $attachData) {
            $order = Order::create([
                'user_id'     => $req->user_id,
                'total_price' => $totalPrice,
            ]);

            $order->products()->attach($attachData);

            return $order;
        });

        return ApiResponse::success(new OrderResource($order), 'order created successfully', 201);
    }

    public function expensive()
    {
        $orders = Order::expensive()->with('products')->paginate($this->pagination);
        return ApiResponse::success($orders, 'get expensive orders', 200); //data.data
    }

    public function trashOrders()
    {
        $orders = Order::onlyTrashed()->paginate($this->pagination);
        return ApiResponse::success($orders, 'get trashed orders successfully', 200); //data.data
    }

    public function destroy($id)
    {
        $order =  Order::findOrFail($id);
        $order->delete();
        return ApiResponse::success(null,'deleted successfully',200);
    }
}
