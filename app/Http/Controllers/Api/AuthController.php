<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);

        $user = User::query()->create($data);

        $accessToken = $user->createToken('auth_token')->plainTextToken;

        return new AuthResource(['user' => $user, 'access_token' => $accessToken]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $accessToken = $user->createToken('auth_token')->plainTextToken;

        return new AuthResource(['user' => $user, 'access_token' => $accessToken]);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
