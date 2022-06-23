<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Marcus Driver',
            'phone' => 81212345678,
            'password' => Hash::make('password'),
            'lat' => -3.31906022,
            'lng' => 114.59135056,
            'address' => 'Jl. Wildan Sari 1 No 11',
            'role' => 'DRIVER',
            'motor_type' => 'VARIO',
            'vehicle_number' => 'DA 123456 RG',
            'is_working' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Ahmad User',
            'phone' => 81237545454,
            'password' => Hash::make('password'),
            'lat' => -3.31906022,
            'lng' => 114.59135056,
            'address' => 'Jl. Wildan Sari 1 No 11',
            'role' => 'USER',
        ]);

        DB::table('users')->insert([
            'name' => 'Risni Admin',
            'phone' => 81212121213,
            'password' => Hash::make('password'),
            'lat' => -3.31906022,
            'lng' => 114.59135056,
            'address' => 'Jl. Wildan Sari 1 No 11',
            'role' => 'ADMIN',
        ]);
    }
}
