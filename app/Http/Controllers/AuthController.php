<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    // Register new user with auto-generated data, CPF, and address
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_id' => 'required|exists:profiles,id',
            ...($request->has('address') ? [
                'address' => 'required|array',
                'address.street' => 'required|string|max:255',
                'address.city' => 'required|string|max:255',
                'address.state' => 'required|string|max:255',
                'address.zip' => 'required|string|max:20',
            ] : []),
            ...($request->has('addresses') ? [
                'addresses' => 'required|array|min:1',
                'addresses.*.street' => 'required|string|max:255',
                'addresses.*.city' => 'required|string|max:255',
                'addresses.*.state' => 'required|string|max:255',
                'addresses.*.zip' => 'required|string|max:20',
            ] : []),
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'password' => Hash::make($request->password),
            'profile_id' => $request->profile_id,
        ]);

        $addressIds = [];   

        if ($request->has('address')) {
            $addressData = $request->input('address');
            $address = Address::create($addressData);
            $addressIds[] = $address->id;
        }

        elseif ($request->has('addresses')) {
            $addressesData = $request->input('addresses');
            foreach ($addressesData as $addressData) {
                $address = Address::create($addressData);
                $addressIds[] = $address->id;
            }
        }

        // Sync the many-to-many realtionship with addresses
        if (!empty($addressIds)) {
            $user->address()->sync($addressIds);
        } else {
            return response()->json([
                'message' => 'No address provided for the user.',
            ], 400);        
        }

        // Create token for API authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('address'),
        ], 201);
    }

    // Login user and issue token
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token for API authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('address'),
        ]);
    }

    // Get authenticated user profile
    public function userProfile(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('address'));
    }

    // When logout revoke tokens
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'User logged out and tokens revoked',
        ]);
    }
}
