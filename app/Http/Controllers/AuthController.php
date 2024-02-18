<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\CytomineAuthService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private CytomineAuthService $cytomineAuthService;

    public function __construct(CytomineAuthService $cytomineAuthService)
    {
        $this->cytomineAuthService = $cytomineAuthService;
    }

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

    public function getCytomineToken(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Not Authenticated'], 403);
        }
        $username = $user->hasRole(['superAdmin', 'operator'])
            ? env('CYTOMINE_ADMIN_USERNAME')
            : $user->username;

        $tokenRes = $this->cytomineAuthService->getUserToken($username);
        if (!$tokenRes['success']) {
            return response()->json(['errors' => $tokenRes['error']], 500);
        }
        return response()->json(['data' => ['token' => $tokenRes['token']]], 200);
    }

}
