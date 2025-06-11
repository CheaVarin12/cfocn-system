<?php

namespace Database\Seeders;

use App\Models\UserExpiry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserExpirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserExpiry::truncate();
        $myRandomString = Str::random(8);
        DB::table('user_expiry')->insert([
            'pass' => $myRandomString,
            'user_id' => 1,
            'expiry_time' =>   Carbon::now()->addMinutes(5)
        ]);
    }
}
