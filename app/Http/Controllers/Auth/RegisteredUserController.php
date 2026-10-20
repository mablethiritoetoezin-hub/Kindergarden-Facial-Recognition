<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\ConsentForm;

class RegisteredUserController extends Controller
{
   public function create()
    {
        // Get the active consent form for display
        $activeConsentForm = ConsentForm::getActive();
        
        return view('auth.register', compact('activeConsentForm'));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
             'role' => 'required|in:teacher,parent,admin',
        ]);

        if ($request->role === 'parent') {
            $request->validate([
                'consent_accepted' => 'required|accepted'
            ], [
                'consent_accepted.required' => 'You must accept the consent form to register as a parent.',
                'consent_accepted.accepted' => 'You must accept the consent form to register as a parent.'
            ]);
        }

        // Get the active consent form
        $activeConsentForm = ConsentForm::getActive();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'parent',
        ];

        // If user is parent and accepted consent, record it
        if ($request->role === 'parent' && $request->consent_accepted && $activeConsentForm) {
            $userData['consent_given'] = true;
            $userData['consent_given_at'] = now();
            $userData['consent_form_id'] = $activeConsentForm->id;
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home');
    }
}