<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsersSeeder extends Seeder {

    public function run()
    {
        $user = User::create(['name' => 'Superuser',
            'email' => 'superuser@super.com',
            'password' => Hash::make('S*per*ser') ]);
    }
}