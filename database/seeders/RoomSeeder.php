<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder{

    public function run(): void
    {
        DB::table('rooms')->insert([
            [
                'room_name' => 'المدرج',
                'capacity' => 70,
                'location' => 'مبنى كلية الحاسبات - الدور الأرضي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_name' => 'قاعة 1/38',
                'capacity' => 20,
                'location' => 'مبنى كلية الحاسبات - الدور الأرضي',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_name' => 'قاعة 1/42',
                'capacity' => 20,
                'location' =>  'مبنى كلية الحاسبات - الدور الأرضي',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
