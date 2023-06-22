<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nrp' => '123456','name' => 'Aga Yanupraba','class' => 'XII.IPA-1','division' => 'RPL','created_at' => now()],
        ];

        DB::table('students')->insert($data);
    }
}
