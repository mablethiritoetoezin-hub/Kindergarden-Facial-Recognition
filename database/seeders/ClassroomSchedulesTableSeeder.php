<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassroomSchedule;
use App\Models\Classroom;

class ClassroomSchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classroom = Classroom::first();

        ClassroomSchedule::create([
            'classroom_id' => $classroom->id,
            'day' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'activity' => 'Arts and Crafts',
        ]);
    }
}
