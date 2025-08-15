<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Domain\Auth\Repositories\AuthRepositoryInterface;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use LaravelLegends\PtBrValidator\Rules\FormatoCep;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API Endpoints for authentication"
 * )
 */
class AuthController extends Controller
{
    protected AuthRepositoryInterface $auth;

    public function __construct(AuthRepositoryInterface $auth)
    {
        $this->auth = $auth;
    }

     /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name","email","cpf","password","profile_id"},
     *             @OA\Property(property="name", type="string", example="Jimmy Starling"),
     *             @OA\Property(property="email", type="string", example="jimmy@example.com"),
     *             @OA\Property(property="cpf", type="string", example="123.456.789-01"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="profile_id", type="integer", example=1),
     *             @OA\Property(property="address", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="street", type="string", example="123 Main St"),
     *                     @OA\Property(property="city", type="string", example="New York"),
     *                     @OA\Property(property="state", type="string", example="NY"),
     *                     @OA\Property(property="zip", type="string", example="68702-190")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => ['required','unique:users', new FormatoCpf],
            'password' => 'required|string|min:8|confirmed',
            'profile_id' => 'required|exists:profiles,id',
            'address' => 'sometimes|array|min:1',
            'address.*.street' => 'required_with:address|string|max:255',
            'address.*.city' => 'required_with:address|string|max:255',
            'address.*.state' => 'required_with:address|string|max:255',
            'address.*.zip' => ['required_with:address', new FormatoCep]//'required_with:address|string|max:20',
        ]);
        $is_authenticated = auth()->check();
        // Call register logic from repository
        $user = $this->auth->register($is_authenticated, $request->all());

        // If not, create token and return json
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login user and get access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="root@root.com"),
     *             @OA\Property(property="password", type="string", example="root")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully logged in"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Logout user and revoke tokens",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out and tokens revoked")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json([
            'message' => 'User logged out and tokens revoked',
        ]);
    }
}
