<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

use function App\Http\helpers\ApiResponse;

class CategoryController extends Controller
{

    function store(CategoryRequest $req)
    {
        $Category = Category::create($req->validated());

        return ApiResponse(new CategoryResource($Category), 'category created successfully', 201);
    }

    function index()
    {
        $categories = Category::with('parent')->get();

        return ApiResponse(CategoryResource::collection($categories), 'get all categories successfully');
    }


    function destroy($id)
    {
        Category::destroy($id);

        return ApiResponse(null, 'category deleted successfully');
    }

    function show($id)
    {
        $category =  Category::with('products')->find($id);

        return ApiResponse(new CategoryResource($category), 'get category successfully');
    }

}
