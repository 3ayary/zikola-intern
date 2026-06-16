<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function App\Http\helpers\ApiResponse;

class ProductController extends Controller
{

    public function index(Request $req)
    {

        $products = Product::select('id', 'name', 'price', 'price_after_discount', 'sku', 'description', 'stock', 'category_id')->with(['category', 'images'])
            ->when($req->search, function ($query) use ($req) {
                $query->whereFullText(['name', 'description'], $req->search);
            })->latest()->paginate($this->pagination);

        return ApiResponse(ProductResource::collection($products), 'get successfully', 200);
    }


    public function store(ProductRequest $req)
    {

        $product = Product::create($req->validated());

        if ($req->hasFile('images')) {
            foreach ($req->file('images') as $index => $image) {
                $path = $image->store('products_images', 'public');

                ProductImages::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_primary' => $index === 0,
                ]);
            }
        }
        return ApiResponse(new ProductResource($product), 'created successfully', 201);
    }

    public function show($id)
    {
        $product = Product::with(['reviews', 'category', 'images'])->findOrFail($id);

        return ApiResponse(new ProductResource($product), 'get successfully', 200);
    }


    public function update(ProductRequest $req, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($req->validated());
        return ApiResponse(new ProductResource($product), 'updated successfully', 200);
    }


    public function destroy($id)
    {

        $deleted =  Product::destroy($id);
        if (!$deleted) {
            return ApiResponse('product not found', 404);
        }
        return ApiResponse(null, 'deleted successfully', 200);
    }

    function attachProducts(Request $req, Category $category)
    {
        $validated = $req->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'required|integer|exists:products,id',
        ]);

        $products = DB::transaction(function () use ($validated, $category) {
            $products = Product::whereIn('id', $validated['product_ids'])->get();

            foreach ($products as $product) {
                $product->update(['category_id' => $category->id]);
            }

            return $products;
        });

        return ApiResponse(ProductResource::collection($products), 'products attached to category successfully', 200);
    }

    function restock(Request $req, $productId)
    {
        $req->validate([
            'stock' => 'required|integer|min:1'
        ]);
        $product = Product::findorFail($productId);

        $product->increment('stock', $req->stock);

        return ApiResponse(new ProductResource($product), 'added new stock successfully', 200);
    }
}
