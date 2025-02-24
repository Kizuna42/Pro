<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfilesTableSeeder extends Seeder
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
                'user_id' => 1,
                'postcode' => '1080014',
                'address' => '東京都港区芝5丁目29-20610',
                'building' => 'クロスオフィス三田',
            ],
            [
                'user_id' => 2,
                'postcode' => '1080014',
                'address' => '東京都港区芝5丁目29-20610',
                'building' => 'クロスオフィス三田',
            ],
            [
                'user_id' => 3,
                'postcode' => '1080014',
                'address' => '東京都港区芝5丁目29-20610',
                'building' => 'クロスオフィス三田',
            ]
        ];

        foreach ($params as $param) {
            Profile::create($param);
        }
    }
}
