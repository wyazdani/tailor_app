<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\Hash;
use \App\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users  =   [
            [
                'name'  =>  'manager',
                'email' =>  'manager@tailor.com',
                'access_token' =>  '',
                'phone_number' =>  '',
                'address' =>  '',
                'password' =>  Hash::make('12345678'),
                'role' =>  'manager',
            ],
            [
                'name'  =>  'customer',
                'email' =>  'customer@tailor.com',
                'access_token' =>  '',
                'phone_number' =>  '',
                'address' =>  '',
                'password' =>  Hash::make('12345678'),
                'role' =>  'customer',
            ],
            [
                'name'  =>  'tailor',
                'email' =>  'tailor@tailor.com',
                'access_token' =>  '',
                'phone_number' =>  '',
                'address' =>  '',
                'password' =>  Hash::make('12345678'),
                'role' =>  'tailor',
            ],
        ];

        foreach ($users as $user)
        {
            User::create($user);
        }
    }
}
