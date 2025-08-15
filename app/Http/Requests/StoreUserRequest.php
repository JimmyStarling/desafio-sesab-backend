<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use LaravelLegends\PtBrValidator\Rules\FormatoCpf;
use LaravelLegends\PtBrValidator\Rules\FormatoCep;
use App\Models\User;

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
        // Se não estiver autenticado, libera apenas para criação de usuário padrão
        if (!$this->user() && $this->input('profile_id') == 3) {
            return true;
        }

        // Se estiver autenticado, verifica permissão no Policy
        return $this->user()
            ? $this->user()->can('create', User::class)
            : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|string|email|max:255|unique:users,email',
            '*.cpf' => ['required','unique:users', new FormatoCpf],
            '*.password' => 'required|string|min:8|confirmed',
            '*.profile_id' => 'required|exists:profiles,id',
            '*.address' => 'sometimes|array|min:1',
            '*.address.*.street' => 'required_with:*.address|string|max:255',
            '*.address.*.city' => 'required_with:*.address|string|max:255',
            '*.address.*.state' => 'required_with:*.address|string|max:255',
            '*.address.*.zip' => ['required_with:*.address', new FormatoCep],
        ];
    }
}
