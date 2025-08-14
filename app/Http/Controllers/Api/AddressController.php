<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Address\Repositories\AddressRepositoryInterface;
use App\Models\Address;

/**
 * @OA\Tag(
 *     name="Adress",
 *     description="API Endpoints of Address"
 * )
 */
class AddressController extends Controller
{
    protected AddressRepositoryInterface $address;

    public function __construct(AddressRepositoryInterface $address)
    {
        $this->address = $address;
    }


    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     tags={"Addresses"},
     *     summary="List all addresses",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of addresses",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Address"))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        return response()->json($this->address->all());
    }

    /**
     * @OA\Post(
     *     path="/api/addresses",
     *     tags={"Addresses"},
     *     summary="Create a new address",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"street","city","state","zip"},
     *             @OA\Property(property="street", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="state", type="string", example="NY"),
     *             @OA\Property(property="zip", type="string", example="10001"),
     *             @OA\Property(property="users", type="array", @OA\Items(type="integer", example=1))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address created",
     *         @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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
