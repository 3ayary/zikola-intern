<?php

namespace App\Services\orderServices;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateOrderService
{

    public function excute(array $productsData)
    {

        return DB::transaction(function () use ($productsData) {
            $calculation =  $this->calculatePrice($productsData);
            $this->StockCheck($calculation['items']);
            $order = $this->saveOrder($calculation['items'], $calculation['total']);
            $this->decrementStock($calculation['items']);
            return $order->load('products');
        });
    }



    function calculatePrice($productsData)
    {
        $total = 0;
        $items = [];
        $productIds = array_column($productsData, 'product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($productsData as $item) {

            $product = $products->get($item['product_id']);

            $total += $product->price * $item['quantity'];

            $items[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }
        return ['total' => $total, 'items' => $items];
    }

    function StockCheck($items)
    {
        foreach ($items as $item) {
            if ($item['product']->stock <= $item['quantity']) {
                throw new \Exception('No stock for product ' . $item['product']->name);
            }
        }
    }

    function decrementStock($items)
    {
        foreach ($items as $item) {
            $item['product']->decrement('stock', $item['quantity']);
        }
    }

    function saveOrder($items, $totalPrice)
    {

        $order = Order::create([
            'user_id'     => Auth::id(),
            'total_price' => $totalPrice
        ]);

        $attachData = [];

        foreach ($items as $item) {

            $attachData[$item['product']->id] = [
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }

        $order->products()->attach($attachData);

        return $order;
    }
}
