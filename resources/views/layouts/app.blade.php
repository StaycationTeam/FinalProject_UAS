<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Strategy Game')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/game.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('game.dashboard') }}">
                🏰 Strategy Game
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('game.dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('kingdom.buildings') }}">
                            <i class="fas fa-building"></i> Buildings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('game.troops') }}">
                            <i class="fas fa-shield-alt"></i> Troops
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('game.battle') }}">
                            <i class="fas fa-fist-raised"></i> Battle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('game.rankings') }}">
                            <i class="fas fa-trophy"></i> Rankings
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-crown"></i> {{ auth()->user()->kingdom->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <span class="dropdown-item-text">
                                        <small>Gold: {{ number_format(auth()->user()->kingdom->gold) }}</small>
                                    </span>
                                </li>
                                <li>
                                    <span class="dropdown-item-text">
                                        <small>Troops: {{ number_format(auth()->user()->kingdom->total_troops) }}</small>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('game.dashboard') }}"><i class="fas fa-user"></i> My Kingdom</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login.form') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.form') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">Game dibuat dari kelompok WFP tahun 2025-2026</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-update resources every 30 seconds
        @auth
        setInterval(function() {
            fetch('{{ route("game.dashboard") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => response.text()).then(html => {
                // You can implement resource update logic here
                console.log('Resources updated');
            });
        }, 30000);
        @endauth

        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    
    @stack('scripts')
</body>
</html>