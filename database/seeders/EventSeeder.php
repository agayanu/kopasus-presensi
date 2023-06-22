<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Latihan Dasar Kepemimpinan Siswa','active' => 'Y','created_at' => now()],
        ];

        DB::table('events')->insert($data);
    }
}
