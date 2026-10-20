@extends('layouts.app')

@section('title', 'My Favorite Moments')
@section('header', 'My Favorite Moments')

@section('content')
    @if ($favorites->isEmpty())
        <div class="alert alert-info">You haven't favorited any photos yet!</div>
    @else
        <div class="row">
            @foreach ($favorites as $photo)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <p><strong>{{ $photo->child->name }}</strong></p>
                            <p class="text-muted">Uploaded by {{ $photo->uploader->name }} on {{ $photo->created_at->format('M d, Y') }}</p>
                            <a href="{{ route('photo.download', $photo->id) }}" class="btn btn-sm btn-outline-primary">Download</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
