<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Photo;
use App\Models\Child;
use App\Models\User;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $child = Child::first();
        $teacher = User::where('role', 'teacher')->first();

        Photo::create([
            'child_id' => $child->id,
            'uploaded_by' => $teacher->id,
            'path' => 'photos/sample.jpg',  // this seeder is just a set up
            'description' => 'Emma painting during class.',
        ]);
    }
}
