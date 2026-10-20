@extends('layouts.app')

@section('title', 'Teacher Dashboard')
@section('header', 'Teacher Dashboard')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4>Welcome, {{ Auth::user()->name }}!</h4>
                    <p>Manage your classroom and upload photos for parents.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Facial Recognition Guidelines -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Facial Recognition Guidelines</h5>
        </div>
        <div class="card-body">
            <h6>Guidelines for Using Facial Recognition in Compliance with Privacy Laws</h6>
            <p>The use of facial recognition technology in our application is designed to enhance security and streamline photo management. Below are the guidelines to ensure compliance with privacy laws and protect the privacy of all users, including students, parents, and staff.</p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Consent:</strong> Obtain explicit, written consent from parents or guardians before using facial recognition for any child’s photos. Consent forms must clearly explain how the technology is used and stored.
                </li>
                <li class="list-group-item">
                    <strong>Data Minimization:</strong> Only collect and process facial data necessary for identifying children in photos for classroom management purposes. Avoid storing unnecessary biometric data.
                </li>
                <li class="list-group-item">
                    <strong>Secure Storage:</strong> All facial recognition data must be encrypted and stored securely in compliance with data protection regulations (e.g., GDPR or COPPA). Access is restricted to authorized administrators only.
                </li>
                <li class="list-group-item">
                    <strong>Transparency:</strong> Inform all users (parents, teachers, and administrators) about the use of facial recognition, including its purpose, data handling procedures, and their rights to opt out.
                </li>
                <li class="list-group-item">
                    <strong>Opt-Out Option:</strong> Provide an easy mechanism for parents to opt out of facial recognition for their children. Photos of opted-out children will be manually tagged instead.
                </li>
                <li class="list-group-item">
                    <strong>Data Retention:</strong> Facial recognition data will be deleted immediately after its purpose (e.g., photo identification) is fulfilled or upon request from a parent or guardian.
                </li>
                <li class="list-group-item">
                    <strong>Third-Party Compliance:</strong> Ensure any third-party facial recognition services comply with privacy laws and do not share data with unauthorized entities.
                </li>
                <li class="list-group-item">
                    <strong>Regular Audits:</strong> Conduct regular audits of facial recognition usage and data storage to ensure ongoing compliance with privacy laws and school policies.
                </li>
            </ul>
            <p class="mt-3">For questions or concerns about these guidelines, please contact the school administration.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="teacherTabs" role="tablist">
                
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'profile' ? 'active' : '' }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="{{ $activeTab == 'profile' ? 'true' : 'false' }}">Profile</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'classroom' ? 'active' : '' }}" id="classroom-tab" data-bs-toggle="tab" data-bs-target="#classroom" type="button" role="tab" aria-controls="classroom" aria-selected="{{ $activeTab == 'classroom' ? 'true' : 'false' }}">Classroom</button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="teacherTabsContent">
                
                <!-- Profile Tab -->
                <div class="tab-pane fade {{ $activeTab == 'profile' ? 'show active' : '' }}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="card border-top-0 rounded-top-0">
                        <div class="card-body">
                            <h5 class="card-title">Profile Information</h5>
                            <form action="{{ route('teacher.update_profile') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password (leave empty to keep current)</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Classroom Tab -->
                <div class="tab-pane fade {{ $activeTab == 'classroom' ? 'show active' : '' }}" id="classroom" role="tabpanel" aria-labelledby="classroom-tab">
                    <div class="card border-top-0 rounded-top-0">
                        <div class="card-body">
                            <h5 class="card-title">Your Classrooms</h5>
                            
                            @if($classrooms->isEmpty())
                                <div class="alert alert-info">
                                    You have not been assigned to any classrooms yet. Please contact the administrator.
                                </div>
                            @else
                                <div class="row">
                                    @foreach($classrooms as $classroom)
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">{{ $classroom->name }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Students:</strong> {{ $classroom->children_count ?? 'N/A' }}</p>
                                                    <p><strong>Age Group:</strong> {{ $classroom->age_group ?? 'N/A' }}</p>
                                                    <p><strong>Room:</strong> {{ $classroom->room_number ?? 'N/A' }}</p>
                                                    
                                                    <h6 class="mt-3">Schedule</h6>
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Day</th>
                                                                <th>Start Time</th>
                                                                <th>End Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($classroom->schedules ?? [] as $schedule)
                                                                <tr>
                                                                    <td>{{ $schedule->day }}</td>
                                                                    <td>{{ $schedule->start_time }}</td>
                                                                    <td>{{ $schedule->end_time }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">No schedule information available</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                    
                                                    <a href="{{ route('teacher.classroom_details', $classroom->id) }}" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Keep the active tab selected after form submissions
        const hash = window.location.hash;
        if (hash) {
            const triggerEl = document.querySelector(`#teacherTabs a[href="${hash}"]`);
            if (triggerEl) {
                bootstrap.Tab.getOrCreateInstance(triggerEl).show();
            }
        }
        
        // Update URL hash when tab changes
        const tabEls = document.querySelectorAll('#teacherTabs button[data-bs-toggle="tab"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', event => {
                window.location.hash = event.target.getAttribute('data-bs-target');
            });
        });
    });
</script>
@endsection