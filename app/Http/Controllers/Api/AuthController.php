<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\AuthResource;
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
}
