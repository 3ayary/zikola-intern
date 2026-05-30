<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    function store(ReviewRequest $req, $id)
    {
        $product = Product::findOrFail($id);
        $review =   $product->reviews()->create([
            ...$req->validated(),
            'user_id' => Auth::id()
        ]);
        return ApiResponse::success(new ReviewResource($review), 'review created', 201);
    }
}
