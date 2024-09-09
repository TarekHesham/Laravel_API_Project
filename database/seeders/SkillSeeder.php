<?php

namespace Database\Seeders;

use App\Models\Dependency\Skills;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            'programming', 'graphic design', 'data analysis', 'project management', 
                'communication', 'seo', 'content writing', 'networking', 'writing',
                'mathematics', 'chemistry', 'physics', 'biology', 'geography',
                'css', 'html', 'javascript', 'php', 'laravel', 'react', 'vue',
                'sql', 'mysql', 'postgresql', 'mongodb', 'aws', 'digital marketing',
                'c++', 'c#', 'python', 'java', 'android', 'ios', 'flutter',
                'ui/ux', 'ux/ui', 'ux', 'ui', 'web development', 'web design',
                'fullstack development', 'frontend development', 'backend development',
                'backend', 'frontend', 'fullstack', 'mobile development', 'game development'
        ];

        foreach ($skills as $skill) {
            Skills::create(['name' => $skill]);
        }
    }
}
