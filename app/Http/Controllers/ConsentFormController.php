<?php

namespace App\Http\Controllers;

use App\Models\ConsentForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of consent forms (Admin only)
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $consentForms = ConsentForm::orderBy('created_at', 'desc')->get();
        return view('admin.consent-forms.index', compact('consentForms'));
    }

    /**
     * Show the form for creating a new consent form (Admin only)
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('admin.consent-forms.create');
    }

    /**
     * Store a newly created consent form (Admin only)
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            ConsentForm::where('is_active', true)->update(['is_active' => false]);
        }

        ConsentForm::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->is_active ?? false
        ]);

        return redirect()->route('consent-forms.index')
            ->with('success', 'Consent form created successfully.');
    }

    /**
     * Show the form for editing the specified consent form (Admin only)
     */
    public function edit(ConsentForm $consentForm)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('admin.consent-forms.edit', compact('consentForm'));
    }

    /**
     * Update the specified consent form (Admin only)
     */
    public function update(Request $request, ConsentForm $consentForm)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            ConsentForm::where('is_active', true)->update(['is_active' => false]);
        }

        $consentForm->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->is_active ?? false
        ]);

        return redirect()->route('consent-forms.index')
            ->with('success', 'Consent form updated successfully.');
    }

    /**
     * Remove the specified consent form (Admin only)
     */
    public function destroy(ConsentForm $consentForm)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $consentForm->delete();

        return redirect()->route('consent-forms.index')
            ->with('success', 'Consent form deleted successfully.');
    }

    /**
     * Activate a specific consent form (Admin only)
     */
    public function activate(ConsentForm $consentForm)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Deactivate all others
        ConsentForm::where('is_active', true)->update(['is_active' => false]);
        
        // Activate this one
        $consentForm->update(['is_active' => true]);

        return redirect()->route('consent-forms.index')
            ->with('success', 'Consent form activated successfully.');
    }
}