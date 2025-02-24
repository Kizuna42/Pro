<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'name' => '出品者A',
                'email' => 'seller_a@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ],
            [
                'name' => '出品者B',
                'email' => 'seller_b@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ],
            [
                'name' => '一般ユーザー',
                'email' => 'general@gmail.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
            ]
        ];

        foreach ($params as $param) {
            User::create($param);
        }
    }
}
