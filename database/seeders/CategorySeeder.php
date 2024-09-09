<?php

namespace Database\Seeders;

use App\Models\Dependency\Categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoies = [
            'technology', 'marketing', 'finance', 'healthcare', 'education', 
            'construction', 'hospitality', 'retail', 'engineering', 'design'
        ];

        foreach ($categoies as $category) {
            Categories::create(['name' => $category]);
        }
    }
}
