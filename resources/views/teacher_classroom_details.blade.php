@extends('layouts.app')

@section('title', 'Classroom Details - ' . $classroom->name)
@section('header', 'Classroom Details')

@section('content')
<div class="container">
    <!-- Back Button -->
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('teacher.dashboard') }}#classroom" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Classroom Information Card -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $classroom->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Age Group:</strong> {{ $classroom->age_group ?? 'N/A' }}</p>
                            <p><strong>Room Number:</strong> {{ $classroom->room_number ?? 'N/A' }}</p>
                            <p><strong>Total Students:</strong> {{ $classroom->children->count() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Description:</strong> {{ $classroom->description ?? 'No description available' }}</p>
                            <p><strong>Capacity:</strong> {{ $classroom->capacity ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Information -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Class Schedule</h5>
                </div>
                <div class="card-body">
                    @if($classroom->schedules && $classroom->schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classroom->schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->day }}</td>
                                            <td>{{ $schedule->start_time }}</td>
                                            <td>{{ $schedule->end_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No schedule information available for this classroom.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Students in This Classroom</h5>
                </div>
                <div class="card-body">
                    @if($classroom->children && $classroom->children->count() > 0)
                        <div class="row">
                            @foreach($classroom->children as $child)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-light">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $child->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    @if($child->age)
                                                        Age: {{ $child->age }} years old
                                                    @endif
                                                    @if($child->date_of_birth)
                                                        <br>DOB: {{ $child->date_of_birth->format('M d, Y') }}
                                                    @endif
                                                </small>
                                            </p>
                                            @if($child->parents && $child->parents->count() > 0)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <strong>Parents:</strong><br>
                                                        @foreach($child->parents as $parent)
                                                            {{ $parent->name }}
                                                            @if(!$loop->last), @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                                        </div>
                    @else
                        <div class="alert alert-info">
                            No students are currently enrolled in this classroom.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Classroom Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('upload') }}" class="btn btn-primary">
                            <i class="fas fa-camera"></i> Upload Photos
                        </a>
                        @if($classroom->children->count() > 0)
                            <a href="{{ route('chats.start') }}" class="btn btn-info">
                                <i class="fas fa-comments"></i> Start Chat with Parents
                            </a>
                        @endif
                        <!-- Add more action buttons as needed -->
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any specific JavaScript for this page if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Auto-refresh or other functionality
        console.log('Classroom details page loaded');
    });
</script>
@endsection