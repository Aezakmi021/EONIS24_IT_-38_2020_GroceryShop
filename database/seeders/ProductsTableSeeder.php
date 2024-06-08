<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $statuses = ['Available', 'Unavailable'];

        foreach (range(1, 10) as $index) {
            DB::table('products')->insert([
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
                'price' => $faker->randomNumber(2),
                'available_quantity' => $faker->randomNumber(2),
                'status' => $statuses[array_rand($statuses)],
                'user_id' => 11, // ID 11 is admin user
                'category_id' => $faker->numberBetween(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
