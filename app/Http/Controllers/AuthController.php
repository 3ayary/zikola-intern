<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function login(LoginRequest $req)
    {

        if (! Auth::attempt($req->validated())) {
            return response()->json([
                'message' => 'invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::success(new AuthResource($token), 'logedin successfully', 200);
    }

    function register(RegisterRequest $req)
    {
        $user = User::create($req->validated());

        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::success(new AuthResource($token), 'user created successfully', 201);
    }

    function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return ApiResponse::success(null, 'user logedout successfully', 200);
    }
}
