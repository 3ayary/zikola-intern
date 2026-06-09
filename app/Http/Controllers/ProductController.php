<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $req)
    {

        $products = Product::select('id', 'name', 'price')->when($req->search, function ($query) use ($req) {
            $query->where('name', 'like', '%' . $req->search . '%');
        })->paginate($this->pagination);

        return ApiResponse::success($products, 'get successfully', 200); //data.data
    }


    public function store(ProductRequest $req)
    {

        $product = Product::create($req->validated());

        return ApiResponse::success(new ProductResource($product), 'created successfully', 201);
    }

    public function show($id)
    {
        $product = Product::with('reviews')->findOrFail($id);

        return ApiResponse::success(new ProductResource($product), 'get successfully', 200);
    }


    public function update(ProductRequest $req, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($req->validated());
        return ApiResponse::success(new ProductResource($product), 'updated successfully', 200);
    }


    public function destroy($id)
    {

        $deleted =  Product::destroy($id);
        if (!$deleted) {
            return ApiResponse::error('product not found', 404);
        }
        return ApiResponse::success(null, 'deleted successfully', 200);
    }
}
