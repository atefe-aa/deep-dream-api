<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @throws BindingResolutionException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenResult = $request->user()->createToken('authToken', ['*'], now()->addDays(15));
        $token = $tokenResult->plainTextToken;
        return response()->json(['api_token' => $token]);
    }

    public function verifyToken(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

}
