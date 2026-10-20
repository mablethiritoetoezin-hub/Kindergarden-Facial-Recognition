@extends('layouts.app')

@section('title', 'Parent Dashboard')
@section('header', 'Parent Dashboard')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4>Welcome, {{ Auth::user()->name }}!</h4>
            <p>View photos of your children below.</p>
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

    <!-- Children and Photos -->
    @if ($children->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body">
                <p class="text-muted">No children linked to your account.</p>
            </div>
        </div>
    @else
        @foreach ($children as $child)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">{{ $child->name }}'s Photos</h5>
                </div>
                <div class="card-body">
                    @if ($child->photos->isEmpty())
                        <p class="text-muted">No photos uploaded for {{ $child->name }}.</p>
                    @else
                        <div class="row">
                            @foreach ($child->photos as $photo)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" alt="Photo" style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <p class="card-text">
                                                <strong>Uploaded By:</strong> {{ $photo->uploader->name }} ({{ ucfirst($photo->uploader->role) }})<br>
                                                <strong>Uploaded At:</strong> {{ $photo->created_at->format('M d, Y H:i') }}
                                            </p>
                                            <div class="card-body">
                                            <p class="card-text">
                                                <strong>Uploaded By:</strong> {{ $photo->uploader->name }} ({{ ucfirst($photo->uploader->role) }})<br>
                                                <strong>Uploaded At:</strong> {{ $photo->created_at->format('M d, Y H:i') }}
                                            </p>
                                            <!-- Favorite Button -->
                                            <form method="POST" action="{{ route('photo.toggleFavorite', $photo->id) }}" class="mb-2">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ auth()->user()->favoritePhotos->contains($photo->id) ? 'btn-warning' : 'btn-outline-primary text-warning border-warning' }}">
                                                    <i class="fas fa-star"></i> Favorite
                                                </button>
                                            </form>

                                            <!-- Dropdown for Actions -->
                                            <div class="dropdown mt-2">
                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                    style="background-color: #001f3f; color: white; border-color: #001f3f;">
                                                    Options
                                                </button>
                                                <ul class="dropdown-menu" style="background-color: #001f3f; color: white;">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('photo.download', $photo->id) }}" target="_blank">
                                                            Download Photo
                                                        </a>
                                                    </li>
                                                    <!-- Future: add "Report", "Favorite", etc. here -->
                                                </ul>
                                            </div>
                                        </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endsection