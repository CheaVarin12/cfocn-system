<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::truncate();

        Project::create(
            [
                'name' => 'Fiber Optic Project',
                'des' => '',
                'vat_tin' => 'L001-100060870',
                'phone' => '085777843',
                'status' => 1
            ]
        );

        Project::create(
            [
                'name' => 'Submarine Project',
                'des' => '',
                'vat_tin' => 'L001-901700659',
                'phone' => '085777843',
                'status' => 1
            ]
        );
        Project::create(
            [
                'name' => 'Build Company App',
                'des' => '',
                'vat_tin' => 'K001-000000001',
                'phone' => '012000001',
                'status' => 2
            ]
        );
        Project::create(
            [
                'name' => 'Underground Project',
                'des' => '',
                'vat_tin' => 'L001-901700661',
                'phone' => '085777843',
                'status' => 1
            ]
        );
    }
}
