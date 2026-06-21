<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\orderServices\CreateOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use function App\Http\helpers\ApiResponse;

class OrderController extends Controller
{
    use AuthorizesRequests;

    private CreateOrderService $createOrderService;

    public  function __construct(CreateOrderService $createOrderService)
    {
        $this->createOrderService = $createOrderService;
    }

    public function index()
    {
        $orders = Order::select('id', 'user_id', 'total_price')->latest()->paginate($this->pagination);

        return ApiResponse(OrderResource::collection($orders), 'get orders successfully', 200);
    }

    public function store(OrderRequest $req)
    {
        $order = $this->createOrderService->excute($req->products);
        return ApiResponse(new OrderResource($order), 'order created successfully', 201);
    }

    public function expensive()
    {
        $orders = Order::expensive()->with('products')->latest()->paginate($this->pagination);
        return ApiResponse(OrderResource::collection($orders), 'get expensive orders', 200);
    }

    public function trashOrders()
    {
        Gate::authorize('view-trashed-only');
        $orders = Order::onlyTrashed()->latest()->paginate($this->pagination);
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
