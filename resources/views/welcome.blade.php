@extends('layouts.app')

@section('title', 'Welcome')
@section('header', 'Welcome to Kindergarten App')

@section('content')
    <div class="text-center">
        <h2 class="mb-4">Kindergarten App</h2>
        <p class="mb-4">Connect with your child's kindergarten activities.</p>
        @if (Auth::guest())
            <a href="{{ route('login') }}" class="btn btn-primary me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        @endif
    </div>
@endsection