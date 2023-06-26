<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['gen' => 17,'active' => 'Y','created_at' => now()],
            ['gen' => 18,'active' => 'Y','created_at' => now()],
            ['gen' => 19,'active' => 'Y','created_at' => now()],
        ];

        DB::table('generations')->insert($data);
    }
}
