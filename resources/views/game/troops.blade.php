@extends('layouts.app')

@section('title', 'Troops - Strategy Game')

@section('content')
<div class="container">
    <div class="game-card">
        <div class="card-header">
            <h5 class="mb-0">Army Management</h5>
        </div>
        <div class="card-body">
            <!-- Troop Overview -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">{{ number_format($kingdom->total_troops) }}</div>
                            <div class="stat-label">Total Troops</div>
                        </div>
                        <div class="stat-card danger">
                            <div class="stat-number">{{ number_format($kingdom->total_attack_power) }}</div>
                            <div class="stat-label">Total Attack Power</div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-number">{{ number_format($kingdom->total_defense_power) }}</div>
                            <div class="stat-label">Total Defense Power</div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-number">{{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }}/min</div>
                            <div class="stat-label">Production Rate</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="game-card h-100">
                        <div class="card-body text-center">
                            <div style="font-size: 3rem;">
                                @switch($kingdom->tribe->name)
                                    @case('Marksman') 🎯 @break
                                    @case('Tank') 🛡️ @break
                                    @case('Mage') 🔮 @break
                                    @case('Warrior') ⚔️ @break
                                @endswitch
                            </div>
                            <h5>{{ $kingdom->tribe->name }} Army</h5>
                            <p class="text-muted">Your elite fighting force</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tribe Attributes -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="game-card">
                        <div class="card-header">
                            <h6 class="mb-0">Tribe Combat Attributes</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Attack Capabilities</h6>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>⚔️ Melee Attack</span>
                                            <strong>{{ $kingdom->tribe->melee_attack }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-danger" style="width: {{ $kingdom->tribe->melee_attack }}%"></div>
                                        </div>
                                    </div>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>🏹 Range Attack</span>
                                            <strong>{{ $kingdom->tribe->range_attack }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-warning" style="width: {{ $kingdom->tribe->range_attack }}%"></div>
                                        </div>
                                    </div>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>🔮 Magic Attack</span>
                                            <strong>{{ $kingdom->tribe->magic_attack }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-info" style="width: {{ $kingdom->tribe->magic_attack }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Defense Capabilities</h6>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>🛡️ Melee Defense</span>
                                            <strong>{{ $kingdom->tribe->melee_defense }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" style="width: {{ $kingdom->tribe->melee_defense }}%"></div>
                                        </div>
                                    </div>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>🎯 Range Defense</span>
                                            <strong>{{ $kingdom->tribe->range_defense }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $kingdom->tribe->range_defense }}%"></div>
                                        </div>
                                    </div>
                                    <div class="attribute-item mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>✨ Magic Defense</span>
                                            <strong>{{ $kingdom->tribe->magic_defense }}</strong>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-purple" style="width: {{ $kingdom->tribe->magic_defense }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="game-card">
                        <div class="card-header">
                            <h6 class="mb-0">Troop Production</h6>
                        </div>
                        <div class="card-body">
                            <div class="production-breakdown">
                                <div class="production-item d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                    <div>
                                        <span class="fw-bold">Tribe Base Rate</span>
                                        <div class="small text-muted">Natural troop training</div>
                                    </div>
                                    <span class="badge bg-primary">{{ $kingdom->tribe->troop_production_rate }}/min</span>
                                </div>
                                
                                <div class="production-item d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                    <div>
                                        <span class="fw-bold">Barracks Production</span>
                                        <div class="small text-muted">{{ $kingdom->barracks_count }} barracks × 5/min</div>
                                    </div>
                                    <span class="badge bg-success">+{{ $kingdom->barracks_count * 5 }}/min</span>
                                </div>
                                
                                <div class="production-item d-flex justify-content-between align-items-center p-3 border rounded">
                                    <div>
                                        <span class="fw-bold">Total Production</span>
                                        <div class="small text-muted">Combined training rate</div>
                                    </div>
                                    <span class="badge bg-warning text-dark">
                                        {{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }}/min
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <small>
                                        <strong>💡 Tip:</strong> Build more barracks to increase your troop production rate. 
                                        Each barracks adds +5 troops per minute to your production.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="game-card">
                        <div class="card-header">
                            <h6 class="mb-0">Combat Calculator</h6>
                        </div>
                        <div class="card-body">
                            <div class="combat-stats">
                                <div class="stat-item mb-3">
                                    <label>Estimated Attack Power per Troop:</label>
                                    <div class="fw-bold text-danger">
                                        {{ number_format(($kingdom->tribe->melee_attack + $kingdom->tribe->range_attack + $kingdom->tribe->magic_attack) / 300, 2) }}
                                    </div>
                                </div>
                                
                                <div class="stat-item mb-3">
                                    <label>Estimated Defense Power per Troop:</label>
                                    <div class="fw-bold text-success">
                                        {{ number_format(($kingdom->tribe->melee_defense + $kingdom->tribe->range_defense + $kingdom->tribe->magic_defense) / 300, 2) }}
                                    </div>
                                </div>
                                
                                <div class="stat-item mb-3">
                                    <label>Current Army Value:</label>
                                    <div class="fw-bold text-primary">
                                        💰 {{ number_format($kingdom->total_troops * 10) }} Gold
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="quick-actions">
                                <h6>Quick Actions</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('kingdom.buildings') }}" class="btn btn-game">
                                        ⚒️ Build More Barracks
                                    </a>
                                    <a href="{{ route('game.battle') }}" class="btn btn-game danger">
                                        ⚔️ Launch Attack
                                    </a>
                                    <a href="{{ route('game.rankings') }}" class="btn btn-game success">
                                        📊 Compare Strength
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Troop Events -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="game-card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Military Events</h6>
                        </div>
                        <div class="card-body">
                            @if($recentBattles->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Event</th>
                                                <th>Troops Before</th>
                                                <th>Troops After</th>
                                                <th>Result</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentBattles as $battle)
                                                <tr>
                                                    <td>
                                                        @if($battle->attacker_id == $kingdom->id)
                                                            <span class="text-danger">Attacked {{ $battle->defender->name }}</span>
                                                        @else
                                                            <span class="text-info">Defended from {{ $battle->attacker->name }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($battle->attacker_id == $kingdom->id)
                                                            {{ number_format($battle->attacker_troops) }}
                                                        @else
                                                            {{ number_format($battle->defender_troops) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($battle->attacker_id == $kingdom->id)
                                                            {{ $battle->result == 'win' ? number_format($battle->attacker_troops) : 0 }}
                                                        @else
                                                            {{ $battle->result == 'win' ? 0 : number_format($battle->defender_troops) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $battle->result == 'win' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ ucfirst($battle->result) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $battle->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center text-muted">No recent military events. Your army awaits your command!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.attribute-item {
    padding: 0.5rem 0;
}

.production-item {
    transition: all 0.3s ease;
}

.production-item:hover {
    transform: translateX(5px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.combat-stats .stat-item {
    padding: 0.5rem;
    border-left: 3px solid #3498db;
    background: rgba(52, 152, 219, 0.1);
}
</style>
@endsection