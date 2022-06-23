<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('restaurants')->insert([
            'name' => 'Bebek Sinjaya',
            'address' => 'Jl. Dahlia',
            'opening_hours' => '18:00:00',
            'closing_hours' => '23:00:00'
        ]);

        DB::table('restaurants')->insert([
            'name' => 'Nasi Goreng & Lalapan Ulun Pian',
            'address' => 'Teluk Dalam',
            'opening_hours' => '18:00:00',
            'closing_hours' => '23:00:00'
        ]);
        
        DB::table('restaurants')->insert([
            'name' => 'Warung Sate Tower',
            'address' => 'Cempaka Raya',
            'opening_hours' => '18:00:00',
            'closing_hours' => '23:00:00'
        ]);
        
        DB::table('restaurants')->insert([
            'name' => 'Warung Berkah jaya',
            'address' => 'Jl. Dahlia',
            'opening_hours' => '18:00:00',
            'closing_hours' => '23:00:00'
        ]);
        
        DB::table('restaurants')->insert([
            'name' => 'Kebab Yasmin',
            'address' => 'Telaga Biru',
            'opening_hours' => '18:00:00',
            'closing_hours' => '23:00:00'
        ]);
    }
}
