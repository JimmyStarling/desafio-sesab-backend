<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile', 'address']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('cpf')) {
            $query->where('cpf', $request->cpf);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        return response()->json($query->paginate(10));
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->address()->sync($request->address);
        return response()->json($user->load('profile', 'address'), 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('profile', 'address'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->address()->sync($request->address);
        return response()->json($user->load('profile', 'address'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
