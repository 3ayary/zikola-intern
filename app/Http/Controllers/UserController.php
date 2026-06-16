<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function App\Http\helpers\ApiResponse;

class UserController extends Controller
{
    function index()
    {
        $users = User::select('id', 'name', 'email')->latest()->paginate($this->pagination);
        return ApiResponse($users, 'get all users successfully', 200); 
    }

    function show()
    {
        $user = Auth::user()->load('profile');
        return ApiResponse(new UserResource($user), 'get user successfully', 200);
    }

    function store(UserRequest $req)
    {
        $user = User::create($req->validated());
        return ApiResponse(new UserResource($user), 'user created successfully', 201);
    }

    function destroy($id)
    {
        $deleted =   User::destroy($id);

        if (!$deleted) {
            return ApiResponse('user not found',404);
        }
        return ApiResponse(null, 'user deleted successfully', 200);
    }

    function update(UserRequest $req, $id)
    {
        $user = User::findOrFail($id);
        $user->update($req->validated());
        return ApiResponse(new UserResource($user), 'user updated successfully', 200);
    }
}
