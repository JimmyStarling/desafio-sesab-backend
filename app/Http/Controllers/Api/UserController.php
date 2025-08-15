<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Models\User;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use LaravelLegends\PtBrValidator\Rules\FormatoCep;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints of Users"
 * )
 */
class UserController extends Controller
{
    use AuthorizesRequests;

    protected UserRepositoryInterface $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="List users",
     *     description="Retrieve a paginated list of users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by user name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter by user email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="cpf",
     *         in="query",
     *         description="Filter by user CPF",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->users->search($request->only(['name', "email", 'cpf', 'start_date', 'end_date']))
        );
    }

    /** DEPRECATED
     * OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *     ),
     *     OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         OA\JsonContent(
     *             OA\Property(property="message", type="string", example="User successfully registered"),
     *             OA\Property(property="access_token", type="string"),
     *             OA\Property(property="token_type", type="string", example="Bearer"),
     *             OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     OA\Response(response=400, description="Validation error")
     * )
    *public function store(StoreUserRequest $request): JsonResponse
    *{
    *
    *    $usersData = $request->all(); // será um array de usuários
    *    $createdUsers = [];
    *   foreach ($usersData as $userData) {
    *       // Autorização para cada usuário
    *       if (!$request->user()->can('create', User::class)) {
    *           return response()->json(['message' => 'Unauthorized'], 403);
    *       }

    *       // Criar usuário
    *       $createdUsers[] = $this->users->create(true, $userData);
    *   }
    *   return response()->json([
    *       'message' => 'Users successfully created',
    *       'users' => $createdUsers
    *   ], 201);
    *}**/

    /**
     * @OA\Post(
     *     path="/api/users/bulk",
     *     tags={"Users"},
     *     summary="Create multiple users",
     *     description="Creates multiple users in a single request. Only authenticated users with proper permissions can perform this action.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/StoreUserRequest")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Users successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Users created successfully"),
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function storeBulk(Request $request)
    {
        $usersData = $request->all(); // ou $request->input('users')
        $createdUsers = $this->users->create(auth()->check(), $usersData);

        return response()->json([
            'message' => 'Users created successfully',
            'users' => $createdUsers
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Get a user by ID",
     *     description="Retrieve user details along with profile and address",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user->load('profile', 'address'));
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Update an existing user and optionally their address",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $updated = $this->users->update(
            $user,
            $request->validated(),
            $request->input('address', [])
        );

        return response()->json($updated);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Delete a specific user by ID\n Only \'Gestor\' and \'Administrador\' can delete. And the Gestor cannot remove Administrator users.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(DeleteUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $this->users->delete($user, $request->validated());

        return response()->json(['message'=>'User sucessfuly removed'], 204);
    }
}
