<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = Profile::all();
        if ($profiles->isEmpty()) {
            $this->command->error('No profiles found. Please run ProfileSeeder first.');
            return;
        }
        // Create a root user with the first profile
        User::create([
            'name' => 'Root',
            'email' => 'root@root.com',
            'password' => bcrypt('root'),
            'cpf' => str_replace([".","-"], "",'000.000.000-00'), // Placeholder CPF
            'profile_id' => $profiles->first()->id,
        ]);
        // Create 10 users with random profiles and addresses
        User::factory()
            ->count(10)
            ->make()
            ->each(function ($user) use ($profiles) {
                $user->profile_id = $profiles->random()->id;
                $user->save();

                $addresses = Address::factory()->count(2)->create();
                $user->address()->attach($addresses->pluck('id')->toArray());
            });
    }
}
