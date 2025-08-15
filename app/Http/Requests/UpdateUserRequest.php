<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Request;

/**
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     type="object",
 *     title="UpdateUser",
 *     description="Update user information",
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
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $this->route('user')->id,
            'cpf'      => 'sometimes|string|max:11|unique:users,cpf,' . $this->route('user')->id,
            'password' => 'sometimes|string|confirmed|min:6',
            'profile_id' => 'sometimes|integer|exists:profiles,id',
        ];
    }
}
