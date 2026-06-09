<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Responses\ApiResponse;
use App\Mail\OtpMail;
use App\Models\User;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        return ApiResponse::success(new AuthResource($token), 'logedin successfully', 200);
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

        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::success(new AuthResource($token), 'user created successfully', 201);
    }

    function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return ApiResponse::success(null, 'user logedout successfully', 200);
    }

    function verifyEmail(Request $req)
    {
        $user = Auth::user();
        if ($user->otp != $req->otp) {
            return ApiResponse::error('invalid OTP', 400);
        }
        if (now()->isAfter($user->otp_expires_at)) {
            return ApiResponse::error('OTP expired', 400);
        }
        $user->update([
            'email_verified_at' => now(),
            'otp'               => null,
            'otp_expires_at'    => null,
        ]);
        return ApiResponse::success(null, 'Email verified successfully');
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

        return ApiResponse::success(null, 'OTP sent to your email');
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
            return ApiResponse::error('Invalid OTP', 400);
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return ApiResponse::error('OTP expired', 400);
        }

        $user->update([
            'password'       => $req->password,
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        return ApiResponse::success(null, 'Password reset successfully');
    }

    function me()
    {
        $user = Auth::user();

        return ApiResponse::success($user, "it's me");
    }
}
