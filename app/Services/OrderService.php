<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getOrdersList($perPage)
    {
        return Order::select('id', 'user_id', 'total_price')->paginate($perPage);
    }

    public function createOrder(array $productsData)
    {
        $totalPrice = 0;
        $attachData = [];

        foreach ($productsData as $item) {
            $product = Product::findOrFail($item['product_id']);
            $attachData[$product->id] = ['quantity' => $item['quantity']];
            $totalPrice += $product->price * $item['quantity'];
        }

        return DB::transaction(function () use ($totalPrice, $attachData) {
            $order = Order::create([
                'user_id'     => Auth::id(),
                'total_price' => $totalPrice,
            ]);

            $order->products()->attach($attachData);

            return $order;
        });
    }

}
