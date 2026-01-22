@extends('layouts.app')

@section('title', 'Infrastructure - Warlord Rising')

@section('content')
<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-0 text-uppercase"><i class="fas fa-city text-blue me-2"></i> Infrastructure</h2>
            <p class="text-secondary mb-0">Construct buildings to increase your economic and military power.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-block bg-dark px-3 py-2 rounded border border-secondary">
                <i class="fas fa-hammer text-gold me-2"></i>
                <span class="text-secondary">Available Gold:</span>
                <span class="text-white fw-bold ms-2">{{ number_format($kingdom->gold) }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-game mb-4 shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-game mb-4 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Main Building -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="building-card h-100 d-flex flex-column">
                <div class="building-icon text-blue">
                    <i class="fas fa-chess-queen"></i>
                </div>
                <h5>Castle Kingdom</h5>
                <p>The heart of your empire. Upgrade to unlock advanced features.</p>
                
                <div class="building-stats mt-auto">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Current Level</span>
                        <span class="text-white fw-bold">{{ $kingdom->main_building_level }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Next Level Cost</span>
                        <span class="text-gold">{{ number_format($buildings->where('type', 'main')->first()->gold_cost * $kingdom->main_building_level) }} G</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('kingdom.upgrade.main') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-game">
                        <i class="fas fa-arrow-up me-2"></i> Upgrade Castle
                    </button>
                </form>
            </div>
        </div>

        <!-- Barracks -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="building-card h-100 d-flex flex-column">
                <div class="building-icon text-danger">
                    <i class="fas fa-dungeon"></i>
                </div>
                <h5>Barracks</h5>
                <p>Training grounds for your troops. More barracks = faster recruitment.</p>
                
                <div class="building-stats mt-auto">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Buildings Owned</span>
                        <span class="text-white fw-bold">{{ $kingdom->barracks_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Production</span>
                        <span class="text-success">+5 troops/min</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Build Cost</span>
                        <span class="text-gold">{{ number_format($buildings->where('type', 'barracks')->first()->gold_cost) }} G</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('kingdom.build.barracks') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-game">
                        <i class="fas fa-plus-circle me-2"></i> Build Barracks
                    </button>
                </form>
            </div>
        </div>

        <!-- Mine -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="building-card h-100 d-flex flex-column" style="border-color: rgba(245, 158, 11, 0.3);">
                <div class="building-icon text-gold" style="background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);">
                    <i class="fas fa-gem"></i>
                </div>
                <h5 class="text-gold">Gold Mine</h5>
                <p>Extracts precious resources from the earth to fund your war.</p>
                
                <div class="building-stats mt-auto">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Mines Active</span>
                        <span class="text-white fw-bold">{{ $kingdom->mines_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Production</span>
                        <span class="text-gold">+10 gold/min</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Build Cost</span>
                        <span class="text-gold">{{ number_format($buildings->where('type', 'mine')->first()->gold_cost) }} G</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('kingdom.build.mine') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-game gold">
                        <i class="fas fa-coins me-2"></i> Build Mine
                    </button>
                </form>
            </div>
        </div>

        <!-- Walls -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="building-card h-100 d-flex flex-column">
                <div class="building-icon text-secondary">
                    <i class="fas fa-shield-virus"></i>
                </div>
                <h5>Defense Walls</h5>
                <p>Fortifications to protect your resources from enemy raids.</p>
                
                <div class="building-stats mt-auto">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Walls Built</span>
                        <span class="text-white fw-bold">{{ $kingdom->walls_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Defense Bonus</span>
                        <span class="text-blue">+10 def/wall</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-secondary">Build Cost</span>
                        <span class="text-gold">{{ number_format($buildings->where('type', 'walls')->first()->gold_cost) }} G</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('kingdom.build.walls') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-game" style="background-color: #475569;">
                        <i class="fas fa-layer-group me-2"></i> Fortify
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Production Summary -->
    <div class="game-card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-chart-pie me-2"></i> Production Overview</span>
            <span class="badge bg-primary">LIVE</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-coins text-gold fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0 text-uppercase letter-spacing-1">Gold Income</h6>
                            <span class="text-gold h4 mb-0 fw-bold font-mono">+{{ 5 + ($kingdom->mines_count * 10) }}</span> <small class="text-secondary">/ minute</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px; background: #334155;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 70%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 text-secondary small">
                        <span>Base: 5</span>
                        <span>Mines: {{ $kingdom->mines_count * 10 }}</span>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-users text-blue fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0 text-uppercase letter-spacing-1">Troop Recruitment</h6>
                            <span class="text-blue h4 mb-0 fw-bold font-mono">+{{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }}</span> <small class="text-secondary">/ minute</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 6px; background: #334155;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 60%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 text-secondary small">
                        <span>Base ({{ $kingdom->tribe->name }}): {{ $kingdom->tribe->troop_production_rate }}</span>
                        <span>Barracks: {{ $kingdom->barracks_count * 5 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection