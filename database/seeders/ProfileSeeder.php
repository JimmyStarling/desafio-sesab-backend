<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            'Administrador',
            'Gerente',
            'Usuário Padrão'
        ];

        foreach ($profiles as $name) {
            Profile::firstOrCreate(['name' => $name]);
        }
    }
}
