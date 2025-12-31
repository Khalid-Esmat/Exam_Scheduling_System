<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Custom list of fake names as requested
        $names = [
            'Ahmed Ali', 'Sara Mansour', 'Omar Hassan', 'Layla Mahmoud', 
            'Youssef Ibrahim', 'Mariam Fawzy', 'Khaled Saeed', 'Nora Osman',
            'Zaid Hamed', 'Mona Zaki', 'Hany Farid', 'Dina Hegazy',
            'Mostafa Bakr', 'Rania Gad', 'Tarek Selim', 'Amira Nasr'
        ];

    foreach ($names as $name) {
    // Adding a random number or timestamp makes the email unique
    $email = Str::slug($name) . rand(100, 999) . '@example.com'; 

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('password123'),
        'role' => 'student',
    ]);


    Student::updateOrCreate(
        ['user_id' => $user->id],
        [
            'level' => rand(1, 4),
            'department_id' => 1,
        ]
    );
}
    }
}