<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = [];

        // Create top-level categories
        $categories[] = [
            'categoryName' => 'Drinks',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Insert top-level category
        DB::table('categories')->insert($categories);

        // Clear the array for re-use
        $categories = [];

        // Create subcategories
        $categories[] = [
            'categoryName' => 'Bread',
            'parent_id' => null, // Parent is "Food And Drinks"
            'created_at' => now(),
            'updated_at' => now()
        ];

        $categories[] = [
            'categoryName' => 'Drinks',
            'parent_id' => 1, // Parent is "Food And Drinks"
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Insert subcategories
        DB::table('categories')->insert($categories);

        // Clear the array for re-use
        $categories = [];

        // Create more specific categories under "Food" and "Drinks"
        $categories[] = [
            'categoryName' => 'Juice',
            'parent_id' => 1, // Parent is "Food"
            'created_at' => now(),
            'updated_at' => now()
        ];

        $categories[] = [
            'categoryName' => 'Milk',
            'parent_id' => 1, // Parent is "Food"
            'created_at' => now(),
            'updated_at' => now()
        ];

        $categories[] = [
            'categoryName' => 'Soda',
            'parent_id' => 1, // Parent is "Drinks"
            'created_at' => now(),
            'updated_at' => now()
        ];

        $categories[] = [
            'categoryName' => 'Water',
            'parent_id' => 1, // Parent is "Drinks"
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Insert additional categories
        DB::table('categories')->insert($categories);
    }
}
