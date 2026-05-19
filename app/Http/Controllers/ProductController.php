<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::select('id', 'name', 'price')->paginate($this->pagination); 

        return ApiResponse::success($products, 'get successfully', 200); //data.data
    }


    public function store(ProductRequest $req)
    {

        $product = Product::create($req->validated());

        return ApiResponse::success(new ProductResource($product), 'created successfully', 201);
    }




    public function update(ProductRequest $req, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($req->validated());
        return ApiResponse::success(new ProductResource($product), 'updated successfully', 200);
    }


    public function destroy($id)
    {

        Product::destroy($id);

        return ApiResponse::success(null, 'deleted successfully', 200);
    }
}
