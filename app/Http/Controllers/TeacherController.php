<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Classroom;
use App\Models\Photo;
use App\Models\Child;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    /**
     * Display the teacher dashboard with the specified active tab
     */
    public function dashboard($activeTab = 'upload')
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (Auth::user()->role !== 'teacher') {
            return redirect()->route('home')->with('error', 'Only teachers can view this dashboard.');
        }

        // Get classrooms assigned to this teacher
        $classrooms = Classroom::where('teacher_id', Auth::id())
            ->withCount('children')
            ->with('schedules')
            ->get();

        if ($classrooms->isEmpty()) {
            $classrooms = collect(); 
        }

        return view('teacher_dashboard', compact('classrooms', 'activeTab'));
    }

    /**
     * Show profile tab
     */
    public function showProfile()
    {
        return $this->dashboard('profile');
    }

    /**
     * Show classroom tab
     */
    public function showClassroom()
    {
        return $this->dashboard('classroom');
    }

    /**
     * Update teacher profile
     */
    public function updateProfile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (Auth::user()->role !== 'teacher') {
            return redirect()->route('home')->with('error', 'Only teachers can update their profile.');
        }

        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show detailed view of a classroom
     */
    public function classroomDetails($classroomId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if (Auth::user()->role !== 'teacher') {
            return redirect()->route('home')->with('error', 'Only teachers can view classroom details.');
        }

        $classroom = Classroom::where('id', $classroomId)
            ->where('teacher_id', Auth::id())
            ->with(['children', 'schedules'])
            ->firstOrFail();

        return view('teacher_classroom_details', compact('classroom'));
    }
}