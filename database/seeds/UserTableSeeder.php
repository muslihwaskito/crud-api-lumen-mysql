<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        	'name' => 'Admin',
        	'identity_id' => '1234567890',
        	'gender' => 1,
        	'address' => 'Jl Jogja - Solo',
        	'photo' => null,
        	'email' => 'admin@gmail.com',
        	'password' => app('hash')->make('secret'),
        	'phone_number' => '08123456789',
        	'api_token' => Str::random(40),
        	'role' => 0,
        	'status' => 1
        ]);
    }
}
