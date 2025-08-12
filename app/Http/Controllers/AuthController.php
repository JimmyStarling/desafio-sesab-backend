<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Domain\Auth\Repositories\AuthRepositoryInterface;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $auth;

    public function __construct(AuthRepositoryInterface $auth)
    {
        $this->auth = $auth;
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_id' => 'required|exists:profiles,id',
            'address' => 'sometimes|array|min:1',
            'address.*.street' => 'required_with:address|string|max:255',
            'address.*.city' => 'required_with:address|string|max:255',
            'address.*.state' => 'required_with:address|string|max:255',
            'address.*.zip' => 'required_with:address|string|max:20',
        ]);

        $user = $this->auth->register($request->all());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = $this->auth->login($request->email, $request->password);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function userProfile(Request $request): JsonResponse
    {
        return response()->json($this->auth->getUserProfile($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json([
            'message' => 'User logged out and tokens revoked',
        ]);
    }
}
