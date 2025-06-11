<?php

namespace Database\Seeders;

use App\Models\FttxCustomerType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostomerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FttxCustomerType::truncate();


        FttxCustomerType::create(
            [
                'name' => '1:32 Normal  (1：32正常)',
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        
        FttxCustomerType::create(
            [
                'name' => 'Borei Order (小区工单)',
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
        
        FttxCustomerType::create(
            [
                'name' => 'Old Order (旧工单)',
                'description' => null,
                'user_id' => 1,
                'status' => 1
            ]
        );
    }
}
