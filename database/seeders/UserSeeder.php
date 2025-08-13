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
