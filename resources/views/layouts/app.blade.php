<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kindergarten App') }} - @yield('title')</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    @yield('styles') <!-- This line allows pages to add their own styles -->

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

</head>



<body class="h-100" style="background: linear-gradient(to bottom, #fff5e6, #ffe4e1);">
    <div class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg" style="background: linear-gradient(to right, #4facfe, #00f2fe);">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">Kindergarten App</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            @if (Auth::user()->role === 'teacher')

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('chats.index') }}">
                                        <i class="fas fa-comments"></i> Chats </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('upload') }}">Upload Photo</a>
                                </li>
                                
                            @elseif (Auth::user()->role === 'parent')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('chats.index') }}">
                                        <i class="fas fa-comments"></i> Chats </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('parent.dashboard') }}">My Child's Photos</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('parent.favorites') }}">
                                    <i class="fas fa-star"></i> Favorites
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('feedback.form') }}">
                                        <i class="fas fa-comment-dots"></i> Feedback
                                    </a>
                                </li>
                            @elseif (Auth::user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.feedback.index') }}">
                                        <i class="fas fa-envelope-open-text"></i> Feedback
                                    </a>
                                </li>

                            @endif
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                    <li>
                                        <a class="dropdown-item" href="
                                            @if(Auth::user()->role === 'teacher')
                                                {{ route('teacher.dashboard') }}
                                            @elseif(Auth::user()->role === 'parent')
                                                {{ route('parent.dashboard') }}
                                            @elseif(Auth::user()->role === 'admin')
                                                {{ route('admin.dashboard') }}
                                            @else
                                                {{ route('home') }}
                                            @endif  
                                        ">Profile</a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Sign out</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <header class="bg-white shadow-sm">
            <div class="container py-4">
                <h1 class="h3 mb-0">@yield('header')</h1>
            </div>
        </header>

        <main class="flex-grow-1">
            <div class="container py-4">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="bg-primary text-white text-center py-3">
            <p>© {{ date('Y') }} Kindergarten App. All rights reserved.</p>
        </footer>

</div>


    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>