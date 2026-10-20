@extends('layouts.app')

@section('title', 'User Feedback')
@section('header', 'User Feedback')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        @foreach ($feedback as $entry)
            <div class="mb-4 border-bottom pb-3">
                <h5 class="mb-1">{{ $entry->subject ?? 'No Subject' }}</h5>
                <p class="mb-1">{{ $entry->message }}</p>
                <small class="text-muted">From: {{ $entry->user->name }} | {{ $entry->created_at->format('F j, Y, g:i a') }}</small>
            </div>
        @endforeach

        {{ $feedback->links() }}
    </div>
</div>
@endsection
