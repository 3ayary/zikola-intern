<?php

namespace App\Services\orderServices;

use App\Models\Order;
use App\Services\orderServices\CreateOrderService ;


class OrderService
{
    public function __construct(protected CreateOrderService $createOrderService)
    {
    }

    public function getOrdersList($perPage)
    {
        return Order::select('id', 'user_id', 'total_price')->paginate($perPage);
    }

    function createOrder($productsData) {

        return $this->createOrderService->excute($productsData);

    }
}
