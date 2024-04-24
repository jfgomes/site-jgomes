<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verify if user already exists
        $existingUser = User::where('email', 'test@test.test')->first();

        // If not, create it
        if (!$existingUser) {
            $user = new User();
            $user->name     = 'Zé Manel';
            $user->email    = 'test@test.test';
            $user->password = Hash::make('Test@123');
            $user->save();
        } else {
            echo "The user already exists.\n";
        }
    }

}
