<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => 'RPL','name' => 'Rekayasa Perangkat Lunak','created_at' => now()],
            ['code' => 'TKJ','name' => 'Teknik Komputer dan Jaringan','created_at' => now()],
            ['code' => 'MM','name' => 'Multimedia','created_at' => now()],
        ];

        DB::table('divisions')->insert($data);
    }
}
