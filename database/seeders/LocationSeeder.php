<?php

namespace Database\Seeders;

use App\Models\Dependency\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'cairo', 'giza', 'alexandria', 'aswan', 'asuit', 'beheira', 'beni suef',
            'dakahlia', 'damietta', 'faiyum', 'gharbia', 'ismailia', 'kafr el-sheikh',
            'luxor', 'matrouh', 'minya', 'monufia', 'new valley', 'north sinai',
            'port said', 'qalyubia', 'qena', 'red sea', 'sharqia', 'sohag', 
            'south sinai', 'suez'
        ];

        foreach ($locations as $location) {
            Location::create([
                'name' => $location
            ]);
        }
    }
}
