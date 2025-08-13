<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected UserRepositoryInterface $users;

    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $this->users->search($request->only(['name', 'cpf', 'start_date', 'end_date']))
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->users->create(
            $request->validated(),
            $request->input('address', [])
        );

        return response()->json($user, 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load('profile', 'address'));
    }

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

    public function destroy(User $user): JsonResponse
    {
        $this->users->delete($user);
        return response()->json(null, 204);
    }
}
