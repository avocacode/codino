<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Codino - Kelas Online Pemrograman Anak</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Playful Navbar -->
        <nav class="navbar navbar-expand-md navbar-custom sticky-top py-3">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fa-solid fa-gamepad me-2 text-primary"></i>Codi<span>no</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
 
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto ms-4">
                        <li class="nav-item">
                            <a class="nav-link font-playful fs-5 text-dark" href="{{ url('/') }}"><i class="fa-solid fa-rocket me-1 text-warning"></i> Katalog Kelas</a>
                        </li>
                    </ul>
 
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item me-2">
                                    <a class="btn btn-link text-decoration-none font-playful fs-5 text-dark" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
 
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-playful-primary font-playful text-white fs-5" href="{{ route('register') }}"><i class="fa-solid fa-user-plus me-1"></i> Daftar Akun</a>
                                </li>
                            @endif
                        @else
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item me-3">
                                    <a class="btn btn-outline-primary border-2 font-playful" href="{{ url('/admin/dashboard') }}">
                                        <i class="fa-solid fa-chart-line me-1"></i> Admin Panel
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->isStudent())
                                <li class="nav-item me-3">
                                    <a class="btn btn-outline-primary border-2 font-playful" href="{{ url('/dashboard') }}">
                                        <i class="fa-solid fa-book-open me-1"></i> Kelas Saya
                                    </a>
                                </li>
                            @endif
                            
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle font-playful text-dark fs-5" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fa-regular fa-face-smile text-success me-1"></i> {{ Auth::user()->name }}
                                </a>
 
                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 15px;" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item py-2" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket text-danger me-2"></i> Keluar
                                    </a>
 
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
 
        <!-- Alerts -->
        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger border-0 shadow-sm rounded-4 alert-dismissible fade show font-playful" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
        
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success border-0 shadow-sm rounded-4 alert-dismissible fade show font-playful" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
 
        <main class="mb-5">
            @yield('content')
        </main>
        
        <!-- Footnote -->
        <footer class="bg-white py-4 border-top">
            <div class="container text-center">
                <p class="mb-0 text-muted font-playful">&copy; 2026 Codino - Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk Programmer Cilik Indonesia.</p>
            </div>
        </footer>
    </div>
</body>
</html>
