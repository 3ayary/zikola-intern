<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

use function App\Http\helpers\ApiResponse;

class ReviewController extends Controller
{
    function store(ReviewRequest $req, $id)
    {
        $product = Product::findOrFail($id);
        $review =   $product->reviews()->create([
            ...$req->validated(),
            'user_id' => Auth::id()
        ]);
        return ApiResponse(new ReviewResource($review), 'review created', 201);
    }
}
