<?php

namespace Database\Seeders;

use App\Models\FttxPosSpeed;
use App\Models\FttxPriceByPosSpeed;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosSpeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FttxPosSpeed::truncate();
        FttxPriceByPosSpeed::truncate();

      $posSpeed1 = FttxPosSpeed::create(
            [
                'split_pos' => '1:32',
                'key_search_import'=>':32',
                'rental_price' => json_encode(["9","7"]),
                'ppcc_price' =>  json_encode(["2","1.5"]),
                'new_install_price' =>  json_encode(["85","108","130","9","7"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed1->id,
                'rental_price_six_month'=>9,
                'rental_price_twelve_month' => 7,
            ]
        );

        
        $posSpeed2 = FttxPosSpeed::create(
            [
                'split_pos' => '1:16',
                'key_search_import'=>':16',
                'rental_price' => json_encode(["15","13"]),
                'ppcc_price' =>  json_encode(["3","2.5"]),
                'new_install_price' =>  json_encode(["85","108","130","15","13"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed2->id,
                'rental_price_six_month'=> 15,
                'rental_price_twelve_month' => 13,
            ]
        );

        $posSpeed3 = FttxPosSpeed::create(
            [
                'split_pos' => '1:8',
                'key_search_import'=>':8',
                'rental_price' => json_encode(["30","25"]),
                'ppcc_price' =>  json_encode(["6","5"]),
                'new_install_price' =>  json_encode(["85","108","130","30","25"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed3->id,
                'rental_price_six_month'=> 30,
                'rental_price_twelve_month' => 25,
            ]
        );

        $posSpeed4 = FttxPosSpeed::create(
            [
                'split_pos' => '1:4',
                'key_search_import'=>':4',
                'rental_price' => json_encode(["50","40"]),
                'ppcc_price' =>  json_encode(["10","8"]),
                'new_install_price' =>  json_encode(["85","108","130","50","40"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed4->id,
                'rental_price_six_month'=> 50,
                'rental_price_twelve_month' => 40,
            ]
        );

        $posSpeed5 = FttxPosSpeed::create(
            [
                'split_pos' => '1:1',
                'key_search_import'=>':1',
                'rental_price' => json_encode(["100","80"]),
                'ppcc_price' =>  json_encode(["20","16"]),
                'new_install_price' =>  json_encode(["85","108","130","100","80"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        
        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed5->id,
                'rental_price_six_month'=> 100,
                'rental_price_twelve_month' => 80,
            ]
        );

        $posSpeed6 =  FttxPosSpeed::create(
            [
                'split_pos' => '1-40M',
                'key_search_import'=>'-40',
                'rental_price' => json_encode(["11","9"]),
                'ppcc_price' =>  json_encode([]),
                'new_install_price' =>  json_encode(["85","108","130","9","7"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed6->id,
                'rental_price_six_month'=> 11,
                'rental_price_twelve_month' => 9,
            ]
        );

        $posSpeed7 = FttxPosSpeed::create(
            [
                'split_pos' => '41-100M',
                'key_search_import'=>'-100',
                'rental_price' => json_encode(["15","13"]),
                'ppcc_price' =>  json_encode([]),
                'new_install_price' =>  json_encode(["85","108","130","15","13"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed7->id,
                'rental_price_six_month'=> 15,
                'rental_price_twelve_month' => 13,
            ]
        );
        $posSpeed8 = FttxPosSpeed::create(
            [
                'split_pos' => '101-150M',
                'key_search_import'=>'-150',
                'rental_price' => json_encode(["50","40"]),
                'ppcc_price' =>  json_encode([]),
                'new_install_price' =>  json_encode(["85","108","130","30","25"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );

        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed8->id,
                'rental_price_six_month'=> 50,
                'rental_price_twelve_month' => 40,
            ]
        );

        $posSpeed9 =  FttxPosSpeed::create(
            [
                'split_pos' => '151-200M',
                'key_search_import' => '-200',
                'rental_price' => json_encode(["60","50"]),
                'ppcc_price' =>  json_encode([]),
                'new_install_price' =>  json_encode(["85","108","130","50","40"]),
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        
        FttxPriceByPosSpeed::create(
            [
                'pos_speed_id' => $posSpeed9->id,
                'rental_price_six_month'=> 60,
                'rental_price_twelve_month' => 50,
            ]
        );
    }
}
