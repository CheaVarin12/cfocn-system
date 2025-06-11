<?php

namespace Database\Seeders;

use App\Models\FttxSettingPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FttxSettingPrice::truncate();


        FttxSettingPrice::create(
            [
                'price' => json_encode([]),
                'type' => config('dummy.setting_price_type.fiber_jumper_fee.key'),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxSettingPrice::create(
            [
                'price' => json_encode([]),
                'type' => config('dummy.setting_price_type.digging_fee.key'),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxSettingPrice::create(
            [
                'price' => json_encode(["0.22","0.50","1.10","1.50"]),
                'type' => config('dummy.setting_price_type.rental_pole.key'),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

    }
}
