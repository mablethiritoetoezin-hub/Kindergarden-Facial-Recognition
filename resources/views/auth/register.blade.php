@extends('layouts.app')

@section('title', 'Register')
@section('header', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" id="registrationForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" type="password" class="form-control" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">{{ __('Role') }}</label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Consent Form Section for Parents -->
                        <div id="consentSection" class="mb-4" style="display: none;">
                            @if($activeConsentForm)
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-file-contract me-2"></i>{{ $activeConsentForm->title }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="consent-content" style="max-height: 300px; overflow-y: auto; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; background-color: #f8f9fa;">
                                            {!! nl2br(e($activeConsentForm->content)) !!}
                                        </div>
                                        
                                        <div class="form-check mt-3">
                                            <input class="form-check-input @error('consent_accepted') is-invalid @enderror" 
                                                   type="checkbox" value="1" id="consent_accepted" name="consent_accepted" 
                                                   {{ old('consent_accepted') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="consent_accepted">
                                                I have read, understood, and agree to the terms and conditions outlined in the consent form above. 
                                                I give permission for my child's photos to be taken and processed as described.
                                            </label>
                                            @error('consent_accepted')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="alert alert-warning mt-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Important:</strong> You must accept this consent form to register as a parent. 
                                            Without your consent, we cannot provide photo sharing services for your child.
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Notice:</strong> No active consent form is available. Please contact the administrator to set up a consent form before parent registration can proceed.
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Register') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const consentSection = document.getElementById('consentSection');
            const form = document.getElementById('registrationForm');
            const consentCheckbox = document.getElementById('consent_accepted');

            function toggleConsentSection() {
                if (roleSelect.value === 'parent') {
                    consentSection.style.display = 'block';
                } else {
                    consentSection.style.display = 'none';
                }
            }

            // Show/hide consent section based on role selection
            roleSelect.addEventListener('change', toggleConsentSection);
            
            // Initialize on page load
            toggleConsentSection();

            // Validate consent acceptance before form submission
            form.addEventListener('submit', function(e) {
                if (roleSelect.value === 'parent' && !consentCheckbox.checked) {
                    e.preventDefault();
                    alert('You must accept the consent form to register as a parent.');
                    consentCheckbox.focus();
                }
            });
        });
    </script>

    <style>
        .consent-content {
            line-height: 1.6;
            font-size: 14px;
        }
        
        .form-check-label {
            line-height: 1.5;
        }
        
        .card-header i {
            font-size: 1.1em;
        }
    </style>
@endsection