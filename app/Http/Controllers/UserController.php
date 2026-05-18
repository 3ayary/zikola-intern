<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;

class UserController extends Controller
{
    function index()
    {
        $users = User::select('id', 'name', 'email')->get();
        return ApiResponse::success(UserResource::collection($users), 'get all users successfully', 200);
    }

    function show($id)
    {
        $user = User::findOrFail($id);
        return ApiResponse::success(new UserResource($user), 'get user successfully', 200);
    }

    function store(UserRequest $req)
    {
        $user = User::create($req->validated());
        return ApiResponse::success(new UserResource($user), 'user created successfully', 201);
    }

    function destroy($id)
    {
        User::destroy($id);
        return ApiResponse::success(null, 'user deleted successfully', 200);
    }

    function update(UserRequest $req, $id)
    {
        $user = User::findOrFail($id);
        $user->update($req->validated());
        return ApiResponse::success(new UserResource($user), 'user updated successfully', 200);
    }
}
