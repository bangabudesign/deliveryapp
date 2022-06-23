<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            'restaurant_id' => 1,
            'name' => 'Ayam Sinjay Nasi + Dada',
            'price' => 26000,
        ]);

        DB::table('products')->insert([
            'restaurant_id' => 1,
            'name' => 'Bebek Sinjay Nasi + Dada',
            'price' => 29000,
        ]);

        DB::table('products')->insert([
            'restaurant_id' => 1,
            'name' => 'Ayam Sinjay Paha',
            'price' => 10000,
        ]);

        DB::table('products')->insert([
            'restaurant_id' => 1,
            'name' => 'Bebek Sinjay Paha',
            'price' => 12000,
        ]);

        DB::table('products')->insert([
            'restaurant_id' => 2,
            'name' => 'Nasi Goreng Seafood',
            'price' => 20000,
        ]);

        DB::table('products')->insert([
            'restaurant_id' => 2,
            'name' => 'Nasi Goreng Spesial',
            'price' => 15000,
        ]);
    }
}
