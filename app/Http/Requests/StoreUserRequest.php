<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $address_field = $this->has('address') ? 'address' : 'addresses';
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|size:11|unique:users,cpf',
            'profile_id' => 'required|exists:profiles,id',
            //'addresses' => 'array',
            //'addresses.*' => 'exists:addresses,id',
            $address_field => 'required|array',
            $address_field . '.*' => 'exists:addresses,id',
        ];
    }
}
