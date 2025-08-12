<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Models\Address;

class AddressController extends Controller
{
    protected AddressRepositoryInterface $address;

    public function __construct(AddressRepositoryInterface $address)
    {
        $this->address = $address;
    }

    public function index()
    {
        return response()->json($this->address->all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city'   => 'required|string|max:100',
            'state'  => 'required|string|max:2',
            'zip'    => 'required|string|max:10',
            'users'  => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);

        return response()->json(
            $this->address->create($validated, $validated['users'] ?? []),
            201
        );
    }

    public function show(Address $address)
    {
        return response()->json($address->load('users'));
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'street' => 'sometimes|string|max:255',
            'city'   => 'sometimes|string|max:100',
            'state'  => 'sometimes|string|max:2',
            'zip'    => 'sometimes|string|max:10',
            'users'  => 'nullable|array',
            'users.*' => 'exists:users,id'
        ]);

        return response()->json(
            $this->address->update($address, $validated, $validated['users'] ?? [])
        );
    }

    public function destroy(Address $address)
    {
        $this->address->delete($address);
        return response()->json(null, 204);
    }
}
