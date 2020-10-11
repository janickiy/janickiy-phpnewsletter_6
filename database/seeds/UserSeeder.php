<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'admin',
            'role' => 'admin' ,
            'login' => 'admin',
            'password' => app('hash')->make('1234567'),
        ]);
    }
}
