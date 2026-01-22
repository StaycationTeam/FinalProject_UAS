<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Strategy Game')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Dark Strategy UI -->
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --border-color: #334155;
            --accent-primary: #3b82f6; /* Blue */
            --accent-secondary: #f59e0b; /* Gold */
            --accent-danger: #ef4444;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --font-main: 'Rajdhani', sans-serif;
            --font-mono: 'Roboto Mono', monospace;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: var(--font-main);
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background-color: rgba(30, 41, 59, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-primary) !important;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            transition: all 0.2s;
            padding: 0.5rem 1rem !important;
            border-radius: 4px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-primary) !important;
            background: rgba(59, 130, 246, 0.1);
        }

        /* Cards */
        .game-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Building Cards */
        .building-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .building-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-primary);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }

        .building-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: rgba(59, 130, 246, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .building-card h5 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .building-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .building-stats {
            background: rgba(0, 0, 0, 0.2);
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            border: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn-game {
            background: var(--accent-primary);
            color: white;
            border: none;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 0.6rem 1.5rem;
            width: 100%;
            transition: all 0.2s;
        }

        .btn-game:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-game.gold {
            background: var(--accent-secondary);
        }

        .btn-game.gold:hover {
            background: #d97706;
            box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
        }

        .btn-game.danger {
            background: var(--accent-danger);
        }

        /* Utilities */
        .text-gold { color: var(--accent-secondary); }
        .text-blue { color: var(--accent-primary); }
        
        .stat-value {
            font-family: var(--font-mono);
            color: var(--text-primary);
        }

        .alert-game {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        .alert-success { border-color: #10b981; color: #10b981; }
        .alert-danger { border-color: var(--accent-danger); color: var(--accent-danger); }

        /* Dropdown */
        .dropdown-menu {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
        }
        
        .dropdown-item {
            color: var(--text-secondary);
        }
        
        .dropdown-item:hover {
            background-color: var(--bg-card-hover);
            color: var(--text-primary);
        }
        
        .dropdown-divider {
            border-color: var(--border-color);
        }
        
        .footer {
            border-top: 1px solid var(--border-color);
            background-color: rgba(30, 41, 59, 0.5) !important;
        }
    </style>
    @yield('style')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('game.dashboard') }}">
                <i class="fas fa-chess-rook text-blue me-2"></i> WARLORD<span class="text-gold">RISING</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('game.dashboard') ? 'active' : '' }}" href="{{ route('game.dashboard') }}">
                            <i class="fas fa-chart-line me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kingdom.buildings') ? 'active' : '' }}" href="{{ route('kingdom.buildings') }}">
                            <i class="fas fa-city me-1"></i> Buildings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('game.troops') ? 'active' : '' }}" href="{{ route('game.troops') }}">
                            <i class="fas fa-users me-1"></i> Army
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('game.battle') ? 'active' : '' }}" href="{{ route('game.battle') }}">
                            <i class="fas fa-skull-crossbones me-1"></i> Battle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('game.training') ? 'active' : '' }}" href="{{ route('game.training') }}">
                            <i class="fas fa-dumbbell me-1"></i> Training
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('game.rankings') ? 'active' : '' }}" href="{{ route('game.rankings') }}">
                            <i class="fas fa-trophy me-1"></i> Leaderboard
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav align-items-center">
                    @auth
                        <!-- Resources Widget -->
                        <li class="nav-item me-3 d-none d-lg-block">
                            <div class="d-flex gap-3 bg-dark px-3 py-1 rounded border border-secondary">
                                <div title="Gold">
                                    <i class="fas fa-coins text-gold me-1"></i>
                                    <span class="stat-value text-gold" id="resource-gold">{{ number_format(auth()->user()->kingdom->gold) }}</span>
                                </div>
                                <div title="Troops">
                                    <i class="fas fa-user-shield text-blue me-1"></i>
                                    <span class="stat-value" id="resource-troops">{{ number_format(auth()->user()->kingdom->total_troops) }}</span>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ substr(auth()->user()->kingdom->name, 0, 1) }}
                                </div>
                                {{ auth()->user()->kingdom->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text text-muted" style="font-size: 0.8rem;">
                                        LOGGED IN AS {{ strtoupper(auth()->user()->username) }}
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('game.dashboard') }}"><i class="fas fa-user me-2"></i> My Kingdom</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login.form') }}">LOGIN</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-game py-1 px-3" href="{{ route('register.form') }}">JOIN WAR</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer py-4 mt-auto">
        <div class="container text-center">
            <small class="text-secondary">WARLORD RISING &copy; 2026 PROJECT WFP GASAL</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-update resources every 10 seconds
        @auth
        setInterval(function() {
            fetch('{{ route("game.resources") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update Navbar Resources
                const goldEl = document.getElementById('resource-gold');
                const troopsEl = document.getElementById('resource-troops');
                
                if (goldEl) goldEl.innerText = data.gold_formatted;
                if (troopsEl) troopsEl.innerText = data.troops_formatted;

                // Also update Dashboard cards if they exist
                const dashGold = document.querySelector('.stat-number.text-gold');
                const dashTroops = document.querySelector('.stat-number.text-blue');

                if (dashGold) dashGold.innerText = data.gold_formatted;
                if (dashTroops) dashTroops.innerText = data.troops_formatted;
            })
            .catch(error => console.error('Error updating resources:', error));
        }, 10000); // Check every 10 seconds
        @endauth
        
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    
    @stack('scripts')
</body>
</html>