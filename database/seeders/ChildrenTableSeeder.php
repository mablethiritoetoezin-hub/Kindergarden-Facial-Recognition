<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Child;
use App\Models\User;
use App\Models\Classroom;

class ChildrenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parent = User::where('role', 'parent')->first();
        $classroom = Classroom::first();

        Child::create([
            'name' => 'Luffy',
            'age' => 8,
            'gender' => 'Male',
            'parent_id' => $parent->id,
            'classroom_id' => $classroom->id,
            'medical_info' => 'No allergies.',
            'notes' => 'Very active and creative.',
        ]);
    }
}
