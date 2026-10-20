<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Consent;
use App\Models\Feedback;
use App\Models\Guideline;
use \App\Models\Child;
use App\Models\Photo;
use App\Models\Classroom;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can view this dashboard.');
        }

        $users = User::all();
        $photos = Photo::with(['child', 'uploader'])->get();
        $classrooms = Classroom::all();
        $teachers = User::where('role','teacher')->get();

        $children = \App\Models\Child::with('parents')->get();
        $parents = User::where('role', 'parent')->with('linkedChildren')->get();
        $guidelines = Guideline::where('key', 'facial_recognition')->first();
        
        
        return view('admin_dashboard', compact('users', 'photos', 'classrooms', 'teachers', 'children', 'parents', 'guidelines'));
    }


    
    public function editUser($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can edit users.');
        }
        
        $user = User::findOrFail($id);
        $classrooms = Classroom::all(); // Add this if you need classrooms in edit form
        
        return view('admin_edit_user', compact('user', 'classrooms'));
    }

    public function updateUser(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can edit users.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'role' => ['required', 'in:teacher,parent,admin'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully.');
    }

    public function destroyUser($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can delete users.');
        }

        if (Auth::id() == $id) {
            return redirect()->route('admin.dashboard')->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.');
    }

    public function deletePhoto($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can delete photos.');
        }

        $photo = Photo::findOrFail($id);
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Photo deleted successfully.');
    }

    public function storeUser(Request $request)
    {
         
        // Authorization check (consistent with your existing pattern)
        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,teacher,parent',
            'classroom_id' => 'nullable|exists:classrooms,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Assign teacher to classroom if applicable
        if ($request->role === 'teacher' && $request->classroom_id) {
            Classroom::where('id', $request->classroom_id)->update(['teacher_id' => $user->id]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully.');
    }

// Classroom Management
    public function manageClassrooms()
    {
        $classrooms = Classroom::with('teacher')->get();
        return view('admin_dashboard', compact('classrooms')); // Update view as needed
    }

    public function storeClassroom(Request $request)
    {
        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|string',
            'room_number' => 'required|string',
            'teacher_id'  => 'required|exists:users,id',
        ]);

        Classroom::create($request->all());
        return redirect()->route('admin.dashboard')->with('success', 'Classroom created successfully.');
    }

    public function editClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        return view('admin_edit_classroom', compact('classroom')); // Create this view
    }

    public function updateClassroom(Request $request, $id)
    {
        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|string',
            'room_number' => 'required|string',
        ]);

        $classroom = Classroom::findOrFail($id);
        $classroom->update($request->all());
        return redirect()->route('admin.dashboard')->with('success', 'Classroom updated successfully.');
    }

    public function destroyClassroom($id)
    {
        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $classroom = Classroom::findOrFail($id);
        $classroom->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Classroom deleted successfully.');
    }

    //linked parents - children

    public function manageChildParentLinks()
    {
        $children = \App\Models\Child::with('parents')->get();
        $parents = User::where('role', 'parent')->with('linkedChildren')->get();

        return view('admin_child_parent_links', compact('children', 'parents'));
    }

    public function attachParentToChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'child_id' => 'required|exists:children,id',
        ]);

        $parent = User::find($request->parent_id);
        $child = Child::find($request->child_id);

        if (!$parent || !$child) {
            return back()->with('error', 'Parent or child not found.');
        }

        if ($parent->linkedChildren()->where('children.id', $child->id)->exists()) {
            return back()->with('error', 'This parent is already linked to this child.');
        }

        $parent->linkedChildren()->attach($child);

        return back()->with('success', 'Parent linked to child successfully.');
    }

    public function detachParentFromChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'child_id' => 'required|exists:children,id',
        ]);

        $parent = User::find($request->parent_id);
        $child = Child::find($request->child_id);

        if (!$parent || !$child) {
            return back()->with('error', 'Parent or child not found.');
        }

        $parent->linkedChildren()->detach($child);

        return back()->with('success', 'Parent unlinked from child successfully.');
    }

    public function editGuidelines()
    {
        // Authorization check 
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Only admins can edit guidelines.');
        }

        $guidelines = Guideline::where('key', 'facial_recognition')->first();
        
        return view('admin_edit_guidelines', compact('guidelines'));
    }

    public function updateGuidelines(Request $request)
    {
        // Authorization check 
        if ((Auth::user()->role ?? 'parent') !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'guidelines' => 'required|array',
            'guidelines.*.title' => 'required|string|max:255',
            'guidelines.*.description' => 'required|string',
        ]);

        $guideline = Guideline::where('key', 'facial_recognition')->first();
        
        if (!$guideline) {
            $guideline = new Guideline();
            $guideline->key = 'facial_recognition';
        }

        $guideline->title = $request->title;
        $guideline->description = $request->description;
        $guideline->content = $request->guidelines;
        $guideline->save();

        return redirect()->route('admin.dashboard')
                        ->with('success', 'Guidelines updated successfully!');
    }


    public function viewFeedback()
    {
        $feedback = Feedback::with('user')->latest()->paginate(10); // or ->get() if pagination isn't needed
        return view('feedback_view', compact('feedback'));
    }

}