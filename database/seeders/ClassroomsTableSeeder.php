<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Classroom;
use App\Models\User;


class ClassroomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();

        Classroom::create([
            'name' => 'Sunshine Room',
            'teacher_id' => $teacher->id,
            'age_group' => '3-4',
            'room_number' => 'A1',
            'capacity' => 20,
            'description' => 'A fun room for toddlers.',
        ]);
    }
}
