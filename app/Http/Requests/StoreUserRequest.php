<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     type="object",
 *     title="Create User Request",
 *     required={"name","email","cpf","password","profile_id"},
 *     @OA\Property(property="name", type="string", example="Jimmy Starling"),
 *     @OA\Property(property="email", type="string", example="jimmy@example.com"),
 *     @OA\Property(property="cpf", type="string", example="670.653.190-16"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     @OA\Property(property="password_confirmation", type="string", example="password123"),
 *     @OA\Property(property="profile_id", type="integer", example=1),
 *     @OA\Property(property="address", type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="street", type="string", example="123 Main St"),
 *             @OA\Property(property="city", type="string", example="New York"),
 *             @OA\Property(property="state", type="string", example="NY"),
 *             @OA\Property(property="zip", type="string", example="68702-190")
 *         )
 *     )
 * )
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('store', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'profile_id' => 'required|exists:profiles,id',
            'address' => 'required|array',
            'address' . '.*' => 'exists:address,id',
        ];
    }
}
