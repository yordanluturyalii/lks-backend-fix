<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=1; $i <= 3; $i++) {
            $user = new User();
            $user->name = "user$i";
            $user->email = "user$i@webtech.id";
            $user->password = "password$i";
            $user->save();
        }
    }
}
