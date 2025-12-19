<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
class AdminUserSeeder extends Seeder{

        public function run(): void   {

            DB::table('users')->insert([
                ['name' => 'مس مارلين',
                'email'=> 'marlian453@gmail.com',
                'role'=>'admin',
                'password' => Hash::make('mr66234r'),
                'created_at'=> now()],
                ['name' => 'عبدالله شاذلى ',
                'email'=> 'abdullahshazly71@gmail.com',
                'role'=>'student',
                'password' => Hash::make('a.g157200'),
                'created_at'=> now()]
            ]);
     
    }
}
