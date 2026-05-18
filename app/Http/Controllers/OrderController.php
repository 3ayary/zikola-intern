<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::select('id', 'user_id', 'total_price')->get();

        return ApiResponse::success(OrderResource::collection($orders), 'get orders successfully', 200);
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
}
