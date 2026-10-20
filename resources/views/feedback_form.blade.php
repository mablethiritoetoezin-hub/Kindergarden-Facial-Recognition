@extends('layouts.app')

@section('title', 'Send Feedback')
@section('header', 'We Value Your Feedback')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-4">Have a suggestion, comment, or just want to say hi? We'd love to hear from you.</p>

        

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('feedback.submit') }}">
                @csrf

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject (optional)</label>
                    <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. App feature, Photo experience" value="{{ old('subject') }}">
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Your Feedback</label>
                    <textarea name="message" id="message" rows="5" class="form-control" placeholder="Share your thoughts..." required>{{ old('message') }}</textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Send Feedback</button>
                </div>
            </form>
        </div>
    </div>
@endsection
