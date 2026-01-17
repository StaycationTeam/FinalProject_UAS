@extends('layouts.app')

@section('title', 'Dashboard - Strategy Game')

@section('style')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Your Kingdom</h5>
                </div>
                <div class="card-body">
                    <h4 class="text-center">{{ $kingdom->name }}</h4>
                    <div class="tribe-badge tribe-{{ strtolower($kingdom->tribe->name) }} text-center mb-3">
                        {{ $kingdom->tribe->name }}
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card gold">
                            <div class="stat-number">{{ number_format($kingdom->gold) }}</div>
                            <div class="stat-label">Gold</div>
                        </div>
                        <div class="stat-card troops">
                            <div class="stat-number">{{ number_format($kingdom->total_troops) }}</div>
                            <div class="stat-label">Troops</div>
                        </div>
                        <div class="stat-card power">
                            <div class="stat-number">{{ number_format($kingdom->total_attack_power) }}</div>
                            <div class="stat-label">Attack Power</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('kingdom.buildings') }}" class="btn btn-game">Buildings</a>
                        <a href="{{ route('game.battle') }}" class="btn btn-game danger">Battle</a>
                        <a href="{{ route('game.rankings') }}" class="btn btn-game success">Rankings</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Kingdom Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Buildings</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Main Building
                                    <span class="badge bg-primary rounded-pill">Level {{ $kingdom->main_building_level }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Barracks
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->barracks_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Mines
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->mines_count }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Walls
                                    <span class="badge bg-primary rounded-pill">{{ $kingdom->walls_count }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Production Rates</h6>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Gold Production
                                    <span class="badge bg-success rounded-pill">{{ 5 + ($kingdom->mines_count * 10) }}/min</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Troop Production
                                    <span class="badge bg-success rounded-pill">{{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }}/min</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Defense Power
                                    <span class="badge bg-info rounded-pill">{{ number_format($kingdom->total_defense_power) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Battles -->
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Battles</h5>
                </div>
                <div class="card-body">
                    @if($recentBattles->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Battle</th>
                                        <th>Result</th>
                                        <th>Gold</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBattles as $battle)
                                        <tr>
                                            <td>
                                                @if($battle->attacker_id == $kingdom->id)
                                                    You attacked {{ $battle->defender->name ?? 'BOT Kingdom' }}
                                                @else
                                                    {{ $battle->attacker->name ?? 'BOT Kingdom' }} attacked you
                                                @endif
                                            </td>
                                            <td>
                                                @if($battle->attacker_id == $kingdom->id)
                                                    <span class="badge {{ $battle->result == 'win' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ucfirst($battle->result) }}
                                                    </span>
                                                @else
                                                    <span class="badge {{ $battle->result == 'win' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $battle->result == 'win' ? 'Lost' : 'Won' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($battle->attacker_id == $kingdom->id && $battle->result == 'win')
                                                    +{{ $battle->gold_stolen }}
                                                @elseif($battle->defender_id == $kingdom->id && $battle->result == 'win')
                                                    -{{ $battle->gold_stolen }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>{{ $battle->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">No battles yet. Go attack someone!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection