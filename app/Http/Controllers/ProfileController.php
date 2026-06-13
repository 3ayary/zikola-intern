<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use function App\Http\helpers\ApiResponse;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    function store(ProfileRequest $req)
    {
        $data = $req->validated();

        if (Auth::user()->profile()->exists()) {

            return ApiResponse('already has profile');
        }

        if ($req->hasFile('avatar')) {
            $data['avatar'] = $req->file('avatar')->store('avatars', 'public');
        }
        $profile = Auth::user()->profile()->create($data);
        return ApiResponse(new ProfileResource($profile), 'profile created successfully', 201);
    }

    function update(ProfileRequest $req)
    {
        $data = $req->validated();
        $profile = Auth::user()->profile;
        $this->authorize('update', $profile);

        if ($req->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $data['avatar'] = $req->file('avatar')->store('avatars', 'public');
        }
        $profile->update($data);
        return ApiResponse(new ProfileResource($profile), 'profile updated successfully', 200);
    }
    public function destroy()
    {
        $profile = Auth::user()->profile;
        $this->authorize('delete', $profile);


        if ($profile->avatar) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $profile->delete();
        return ApiResponse(null, 'profile deleted successfully', 200);
    }
}
