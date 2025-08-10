<?php

namespace App\Http\Controllers\Api;

use App\Model\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile', 'addresses']);

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
        $user->addresses()->sync($request->addresses);
        return response()->json($user->load('profile', 'addresses'), 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('profile', 'addresses'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->addresses()->sync($request->addresses);
        return response()->json($user->load('profile', 'addresses'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
