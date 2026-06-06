<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use App\Services\OrderService;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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

        return ApiResponse::success($orders, 'get orders successfully', 200);  //data.data
    }

    public function store(OrderRequest $req)
    {
        $order = $this->orderService->createOrder($req->products);
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
        $this->authorize('delete', $order);
        $order->delete();
        return ApiResponse::success(null, 'deleted successfully', 200);
    }

    public function updateStatus(Request $req, $id)
    {
        $req->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $req->status]);
        
        $order->user->notify(new OrderStatusChanged($order));
        return ApiResponse::success(new OrderResource($order), 'status updated successfully');
    }
}
