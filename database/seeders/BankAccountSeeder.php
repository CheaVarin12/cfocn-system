<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BankAccount::truncate();

        BankAccount::create(
            [
                'bank_name' => 'CANADIA BANK PLC. A/C',
                'account_name' => '',
                'account_number' => '001-0000117418',
                'status' => 1,
            ]
        );

        BankAccount::create(
            [
                'bank_name' => 'ABA BANK',
                'account_name' => '',
                'account_number' => '001429829',
                'status' => 1,
            ]
        );
    }
}
