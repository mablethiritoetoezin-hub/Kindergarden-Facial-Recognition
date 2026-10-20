<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\TrainingPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingPhotoController extends Controller
{
    public function store(Request $request, $childId)
    {
        $request->validate([
            'photo' => 'required|image',
        ]);

        $child = Child::findOrFail($childId);

        // Create directory for child_id if it doesn't exist
        $directory = "training_faces/{$child->id}";
        Storage::makeDirectory($directory);

        // Store the photo in training_faces/{child_id}/
        $photoPath = $request->file('photo')->store($directory);

        // Save record to training_photos table
        TrainingPhoto::create([
            'child_id' => $child->id,
            'photo_path' => $photoPath,
        ]);

        return back()->with('success', 'Training photo uploaded.');
    }

    public function train()
    {
        shell_exec(escapeshellarg(config('services.python_bin')) . ' ' . escapeshellarg(base_path('train.py')));
        return back()->with('success', 'Model trained successfully.');
    }
}