<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Profile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Default password of all users
            'remember_token' => Str::random(10),
            'profile_id' => null,
            'cpf' => $this->generateValidCpf(),
        ];
    }

    /**
     * Indicate that the model's CPF should be valid.
     */
    private function generateValidCpf()
    {
        // Gera os 9 primeiros dígitos
        $cpf = [];
        for ($i = 0; $i < 9; $i++) {
            $cpf[] = rand(0, 9);
        }

        // Calcula primeiro dígito verificador
        $sum = 0;
        for ($i = 0, $weight = 10; $i < 9; $i++, $weight--) {
            $sum += $cpf[$i] * $weight;
        }
        $remainder = $sum % 11;
        $cpf[] = ($remainder < 2) ? 0 : 11 - $remainder;

        // Calcula segundo dígito verificador
        $sum = 0;
        for ($i = 0, $weight = 11; $i < 10; $i++, $weight--) {
            $sum += $cpf[$i] * $weight;
        }
        $remainder = $sum % 11;
        $cpf[] = ($remainder < 2) ? 0 : 11 - $remainder;

        return implode('', $cpf);
        //return vsprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', $cpf);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
