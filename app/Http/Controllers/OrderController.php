<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\orderServices\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

use function App\Http\helpers\ApiResponse;

class OrderController extends Controller
{
    use AuthorizesRequests;

    protected OrderService $orderService;

    public  function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getOrdersList($this->pagination);

        return ApiResponse(OrderResource::collection($orders), 'get orders successfully', 200);
    }

    public function store(OrderRequest $req)
    {
        $order = $this->orderService->createOrder($req->products);
        return ApiResponse(new OrderResource($order), 'order created successfully', 201);
    }

    public function expensive()
    {
        $orders = Order::expensive()->with('products')->paginate($this->pagination);
        return ApiResponse(OrderResource::collection($orders), 'get expensive orders', 200);
    }

    public function trashOrders()
    {
        $orders = Order::onlyTrashed()->paginate($this->pagination);
        return ApiResponse(OrderResource::collection($orders), 'get trashed orders successfully', 200);
    }

    public function destroy($id)
    {
        $order =  Order::findOrFail($id);
        $this->authorize('delete', $order);
        $order->delete();
        return ApiResponse(null, 'deleted successfully', 200);
    }

    public function updateStatus(Request $req, $id)
    {
        $req->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $req->status]);

        return ApiResponse(new OrderResource($order), 'status updated successfully');
    }
}
