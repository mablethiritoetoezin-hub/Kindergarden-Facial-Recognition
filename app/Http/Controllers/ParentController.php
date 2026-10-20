<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Child;
use App\Models\Photo;



class ParentController extends Controller
{

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        if ((Auth::user()->role ?? 'parent') !== 'parent') {
            return redirect()->route('home')->with('error', 'Only parents can view this dashboard.');
        }

            
        $children = Auth::user()->children()->with(['photos' => function ($query) {
            $query->with('uploader');
        }])->get();

        return view('parent_dashboard', compact('children'));
    }

    public function favorites()
    {
        $favorites = Auth::user()->favoritePhotos()->with('child', 'uploader')->get();
        return view('parent_favorites', compact('favorites'));
    }

}