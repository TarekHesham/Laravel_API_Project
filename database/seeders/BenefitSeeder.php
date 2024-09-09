<?php

namespace Database\Seeders;

use App\Models\Dependency\Benefits;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BenefitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = [
            'health insurance', 'paid time off', 'remote work', 'flexible hours', 
            'gym membership', 'retirement plan', 'bonus', 'professional development'
        ];
        foreach ($benefits as $benefit) {
            Benefits::create(['name' => $benefit]);
        }
    }
}
