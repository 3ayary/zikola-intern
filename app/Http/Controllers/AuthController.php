<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use function App\Http\helpers\ApiResponse;

class AuthController extends Controller
{

    function login(LoginRequest $req)
    {


        $token = Auth::guard('api')->attempt($req->validated());

        if (! $token) {
            return response()->json([
                'message' => 'invalid credentials',
            ], 401);
        }

        return ApiResponse(new AuthResource($token), 'logedin successfully', 200);
    }

    function register(RegisterRequest $req)
    {

        $user = DB::transaction(function () use ($req) {
            $otp = rand(100000, 999999);
            $user = User::create(
                [
                    ...$req->validated(),
                    'otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(10)
                ]
            );

            Mail::to($user->email)->send(new OtpMail($otp));

            return $user;
        });
        $token = Auth::guard('api')->login($user);

        return ApiResponse(new AuthResource($token), 'user created successfully', 201);
    }

    function logout()
    {
        Auth::logout();
        return ApiResponse(null, 'user logedout successfully', 200);
    }

    function verifyEmail(Request $req)
    {
        $user = Auth::user();
        if ($user->otp != $req->otp) {
            return ApiResponse('invalid OTP', 400);
        }
        if (now()->isAfter($user->otp_expires_at)) {
            return ApiResponse('OTP expired', 400);
        }
        $user->update([
            'email_verified_at' => now(),
            'otp'               => null,
            'otp_expires_at'    => null,
        ]);
        return ApiResponse(null, 'Email verified successfully');
    }

    function forgotPassword(Request $req)
    {
        $req->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $req->email)->first();
        $otp = rand(100000, 999999);
        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);
        Mail::to($user->email)->send(new OtpMail($otp));

        return ApiResponse(null, 'OTP sent to your email');
    }

    public function resetPassword(Request $req)
    {
        $req->validate([
            'email'    => 'required|email|exists:users,email',
            'otp'      => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $req->email)->first();

        if ($user->otp != $req->otp) {
            return ApiResponse('Invalid OTP', 400);
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return ApiResponse('OTP expired', 400);
        }

        $user->update([
            'password'       => $req->password,
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        return ApiResponse(null, 'Password reset successfully');
    }

    function me()
    {
        $user = Auth::user();

        return ApiResponse($user, "it's me");
    }
}
