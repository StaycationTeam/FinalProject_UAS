@extends('layouts.app')

@section('title', 'Dashboard - Warlord Rising')

@section('style')
<style>
    .list-group-item {
        background-color: rgba(30, 41, 59, 0.4);
        border-color: #334155;
        color: #f1f5f9;
    }
    .stat-card {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid #334155;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1rem;
    }
    .stat-number {
        font-family: 'Roboto Mono', monospace;
        font-size: 1.5rem;
        font-weight: bold;
    }
    .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
    }
    .tribe-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="game-card">
                <div class="card-header text-center">
                    <h5 class="mb-0">COMMAND CENTER</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="mb-2">
                            <i class="fas fa-crown text-gold fa-3x"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $kingdom->name }}</h3>
                        <div class="tribe-badge mb-3">
                            {{ $kingdom->tribe->name }}
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-number text-gold">{{ number_format($kingdom->gold) }}</div>
                                <div class="stat-label">Gold</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-number text-blue">{{ number_format($kingdom->total_troops) }}</div>
                                <div class="stat-label">Troops</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card">
                                <div class="stat-number text-danger">{{ number_format($kingdom->total_attack_power) }}</div>
                                <div class="stat-label">Attack Power</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('kingdom.buildings') }}" class="btn btn-game">
                            <i class="fas fa-hammer me-2"></i> Buildings
                        </a>
                        <a href="{{ route('game.battle') }}" class="btn btn-game danger">
                            <i class="fas fa-skull me-2"></i> Battle
                        </a>
                        <a href="{{ route('game.rankings') }}" class="btn btn-game" style="background-color: #10b981;">
                            <i class="fas fa-trophy me-2"></i> Rankings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Kingdom Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary text-uppercase mb-3 ps-2 border-start border-3 border-primary">Infrastructure</h6>
                            <ul class="list-group list-group-flush rounded mb-4">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-chess-queen me-2 text-secondary"></i> Castle Level</span>
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->main_building_level }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-dungeon me-2 text-secondary"></i> Barracks</span>
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->barracks_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-gem me-2 text-secondary"></i> Mines</span>
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->mines_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-shield-virus me-2 text-secondary"></i> Walls</span>
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->walls_count }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary text-uppercase mb-3 ps-2 border-start border-3 border-success">Production & Defense</h6>
                            <ul class="list-group list-group-flush rounded">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-coins me-2 text-gold"></i> Gold Income</span>
                                    <span class="badge bg-success rounded-pill font-mono">+{{ 5 + ($kingdom->mines_count * 10) }}/min</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-users me-2 text-blue"></i> Troop Recruitment</span>
                                    <span class="badge bg-success rounded-pill font-mono">+{{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }}/min</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-shield-alt me-2 text-secondary"></i> Total Defense</span>
                                    <span class="badge bg-info rounded-pill font-mono">{{ number_format($kingdom->total_defense_power) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Battles -->
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Recent Battles</h5>
                </div>
                <div class="card-body">
                    @if($recentBattles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                <thead>
                                    <tr class="text-secondary text-uppercase" style="font-size: 0.8rem;">
                                        <th>Battle Detail</th>
                                        <th>Outcome</th>
                                        <th>Loot</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBattles as $battle)
                                        @php
                                            $isAttacker = $battle->attacker_id == $kingdom->id;
                                            $didWin = $battle->winner_id == $kingdom->id;
                                        @endphp
                                        <tr style="border-color: #334155;">
                                            <td class="align-middle">
                                                @if($isAttacker)
                                                    <span class="text-danger">{{ $battle->attacker->name ?? 'You' }}</span> attacked <span class="fw-bold">{{ $battle->defender->name ?? 'AI Opponent' }}</span>
                                                @else
                                                    <span class="fw-bold text-danger">{{ $battle->attacker->name ?? 'AI Opponent' }}</span> attacked you
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($didWin)
                                                    <span class="badge bg-success">
                                                        {{ $isAttacker ? 'VICTORY' : 'DEFENSE SUCCESS' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        {{ $isAttacker ? 'DEFEAT' : 'DEFENSE FAILED' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle font-mono">
                                                @if($isAttacker && $didWin)
                                                    <span class="text-gold">+{{ number_format($battle->gold_stolen) }}</span>
                                                @elseif(!$isAttacker && !$didWin)
                                                    <span class="text-danger">-{{ number_format($battle->gold_stolen) }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-secondary small">{{ $battle->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-peace text-secondary fa-3x mb-3"></i>
                            <p class="text-secondary">No battles recorded yet.</p>
                            <a href="{{ route('game.battle') }}" class="btn btn-outline-primary btn-sm">Start a War</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection