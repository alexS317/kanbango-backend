<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // Register and login simultaneously
        $token = $user->createToken('auth_token')->plainTextToken;

        return new SuccessResource([
            'message' => 'User registered.',
            'data' => new AuthResource([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]),
        ]);
    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return new ErrorResource([
                'message' => 'Invalid credentials.',
                'status_code' => 401,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new SuccessResource([
            'message' => 'User logged in.',
            'data' => new AuthResource([
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return new SuccessResource([
            'message' => 'User logged out.',
        ]);
    }

    public function logoutAllDevices(Request $request) {
        $request->user()->tokens()->delete();

        return new SuccessResource([
            'message' => 'User logged out of all devices.',
        ]);
    }

    public function user(Request $request)
    {
        return new SuccessResource([
            'message' => 'User data retrieved successfully.',
            'data' => new UserResource($request->user()),
        ]);
    }
}
