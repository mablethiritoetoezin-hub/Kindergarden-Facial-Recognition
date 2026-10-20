<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Photo;
use App\Models\User;
use App\Models\Child;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    public function showUploadForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (Auth::user()->role !== 'teacher') {
            return redirect()->route('home')->with('error', 'Only teachers can upload photos.');
        }

        return view('upload_photo');
    }

    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:10240', // 10MB max per image
            'child_id' => 'nullable|exists:children,id',
            'caption' => 'nullable|string|max:500',
        ]);

        $uploadedPhotos = [];
        $processedChildren = [];
        $errors = [];

        foreach ($request->file('photos') as $index => $photo) {
            try {
                // Store the uploaded photo
                $photoPath = $photo->store('photos', 'public');
                
                Log::info('PhotoPath: ' . $photoPath);
                
                $fullPhotoPath = storage_path('app/public/' . $photoPath);
                $fullPhotoPath = str_replace('\\', DIRECTORY_SEPARATOR, $fullPhotoPath);
                
                Log::info('Full photo path (storage_path): ' . $fullPhotoPath);
                
                if (!file_exists($fullPhotoPath)) {
                    Log::error('File does NOT exist at: ' . $fullPhotoPath);
                    $errors[] = "Photo " . ($index + 1) . " could not be saved properly.";
                    continue;
                } else {
                    Log::info('File exists at: ' . $fullPhotoPath);
                }

                $childId = null;

                if ($request->child_id) {
                    // Use the manually selected child_id
                    $childId = $request->child_id;
                } else {
                    // No child_id provided, trigger AI detection
                    $pythonPath = config('services.python_bin');
                    $scriptPath = base_path('recognize.py');
                    $command = escapeshellarg($pythonPath) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($fullPhotoPath);
                    $output = shell_exec($command);
                    $childId = trim($output);
                    
                    Log::info("AI Detection for photo $index: Command = $command, Output = $output");

                    // Handle case where AI doesn't recognize a child
                    if (!$childId || $childId === "No match found" || !is_numeric($childId)) {
                        $errors[] = "Photo " . ($index + 1) . ": No matching child found.";
                        // Delete the uploaded file since we can't process it
                        Storage::disk('public')->delete($photoPath);
                        continue;
                    }
                }

                // Create photo record
                $photoRecord = Photo::create([
                    'child_id' => $childId,
                    'path' => $photoPath,
                    'uploaded_by' => auth()->id(),
                ]);

                $uploadedPhotos[] = [
                    'photo_record' => $photoRecord,
                    'path' => $photoPath,
                    'child_id' => $childId
                ];

                // Track processed children to avoid duplicate messages
                if (!in_array($childId, $processedChildren)) {
                    $processedChildren[] = $childId;
                }

            } catch (\Exception $e) {
                Log::error("Error processing photo $index: " . $e->getMessage());
                $errors[] = "Photo " . ($index + 1) . ": Processing failed - " . $e->getMessage();
            }
        }

        // If no photos were successfully processed
        if (empty($uploadedPhotos)) {
            return back()->withErrors(['photos' => 'No photos could be processed successfully.'])
                        ->withInput();
        }

        // Send messages to parents for each processed child
        $caption = $request->caption ?: null;
        
        foreach ($processedChildren as $childId) {
            try {
                $child = Child::findOrFail($childId);
                $parents = $child->parents()->get();
                
                // Get all photo paths for this child from this upload
                $childPhotoPaths = collect($uploadedPhotos)
                    ->where('child_id', $childId)
                    ->pluck('path')
                    ->toArray();

                foreach ($parents as $parent) {
                    $chat = Chat::firstOrCreate([
                        'teacher_id' => auth()->id(),
                        'parent_id' => $parent->id,
                    ]);

                    // Create message with caption or default message
                    $message = $caption ?: 'Here is a photo of ' . $child->name;
                    
                    ChatMessage::create([
                        'chat_id' => $chat->id,
                        'sender_id' => auth()->id(),
                        'message' => $message,
                        'photos' => $childPhotoPaths,
                        'sent_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error sending messages for child $childId: " . $e->getMessage());
                $errors[] = "Failed to send messages for one or more children.";
            }
        }

        // Prepare success message
        $successMessage = count($uploadedPhotos) . ' photo(s) uploaded successfully';
        if (count($processedChildren) > 1) {
            $successMessage .= ' for ' . count($processedChildren) . ' children';
        }
        $successMessage .= ' and messages sent to parents.';

        // Return with success and any errors
        if (!empty($errors)) {
            return redirect()->back()
                           ->with('success', $successMessage)
                           ->with('warnings', $errors);
        }

        return redirect()->back()->with('success', $successMessage);
    }

    public function download(Photo $photo)
    {
        // Ensure the user is a parent
        if (Auth::user()->role !== 'parent') {
            abort(403, 'Unauthorized');
        }

        // Ensure the parent is linked to the child in the photo
        $child = $photo->child;

        if (!$child || !$child->parents->contains(Auth::user()->id)) {
            abort(403, 'You do not have permission to download this photo.');
        }

        $filePath = storage_path('app/public/' . $photo->path);

        if (!file_exists($filePath)) {
            abort(404, 'Photo file not found.');
        }

        return response()->download($filePath, basename($photo->path));
    }


    public function toggleFavorite(Photo $photo)
    {
        $user = auth()->user();

        if ($user->favoritePhotos->contains($photo->id)) {
            $user->favoritePhotos()->detach($photo->id);
        } else {
            $user->favoritePhotos()->attach($photo->id);
        }

        return back(); 
    }
}