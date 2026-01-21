@extends('layouts.app')

@section('title', 'Rankings - Strategy Game')

@section('content')
<div class="container">
    <div class="game-card">
        <div class="card-header">
            <h5 class="mb-0">Kingdom Rankings</h5>
        </div>
        <div class="card-body">
            <!-- Ranking Filters -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <a href="?sort=attack" class="btn btn-outline-primary {{ request('sort', 'attack') == 'attack' ? 'active' : '' }}">
                            Attack Power
                        </a>
                        <a href="?sort=defense" class="btn btn-outline-primary {{ request('sort') == 'defense' ? 'active' : '' }}">
                            Defense Power
                        </a>
                        <a href="?sort=gold" class="btn btn-outline-primary {{ request('sort') == 'gold' ? 'active' : '' }}">
                            Wealth
                        </a>
                        <a href="?sort=troops" class="btn btn-outline-primary {{ request('sort') == 'troops' ? 'active' : '' }}">
                            Troops
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search kingdoms..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-game">Search</button>
                    </form>
                </div>
            </div>

            @if($kingdoms->count() > 0)
                <div class="table-responsive">
                    <table class="table ranking-table">
                        <thead>
                            <tr>
                                <th width="60">Rank</th>
                                <th>Kingdom</th>
                                <th>Tribe</th>
                                <th>Player</th>
                                <th>Attack Power</th>
                                <th>Defense Power</th>
                                <th>Troops</th>
                                <th>Gold</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kingdoms as $index => $kingdom)
                                <tr class="{{ $kingdom->id == auth()->user()->kingdom->id ? 'table-active' : '' }}">
                                    <td>
                                        @if($index < 3)
                                            <div class="rank-badge rank-{{ $index + 1 }}">
                                                {{ $index + 1 }}
                                            </div>
                                        @else
                                            <div class="rank-number">
                                                {{ $index + 1 + (($kingdoms->currentPage() - 1) * $kingdoms->perPage()) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="kingdom-avatar me-2" style="font-size: 1.5rem;">
                                                @switch($kingdom->tribe->name)
                                                    @case('Marksman') 🎯 @break
                                                    @case('Tank') 🛡️ @break
                                                    @case('Mage') 🔮 @break
                                                    @case('Warrior') ⚔️ @break
                                                    @default 🏰
                                                @endswitch
                                            </div>
                                            <div>
                                                <strong>{{ $kingdom->name }}</strong>
                                                @if($kingdom->id == auth()->user()->kingdom->id)
                                                    <span class="badge bg-info ms-1">You</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="tribe-badge tribe-{{ strtolower($kingdom->tribe->name) }}">
                                            {{ $kingdom->tribe->name }}
                                        </span>
                                    </td>
                                    <td>{{ $kingdom->user->username }}</td>
                                    <td>
                                        <div class="power-value">
                                            {{ number_format($kingdom->total_attack_power) }}
                                            @if($kingdom->total_attack_power > 0)
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar bg-danger" style="width: {{ min(100, ($kingdom->total_attack_power / 1000) * 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="power-value">
                                            {{ number_format($kingdom->total_defense_power) }}
                                            @if($kingdom->total_defense_power > 0)
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar bg-success" style="width: {{ min(100, ($kingdom->total_defense_power / 1000) * 100) }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="troop-count">{{ number_format($kingdom->total_troops) }}</span>
                                    </td>
                                    <td>
                                        <span class="gold-count">💰 {{ number_format($kingdom->gold) }}</span>
                                    </td>
                                    <td>
                                        @if($kingdom->id != auth()->user()->kingdom->id)
                                            <a href="{{ route('game.battle') }}?target={{ $kingdom->id }}" 
                                               class="btn btn-sm btn-outline-danger">
                                                Attack
                                            </a>
                                        @else
                                            <span class="text-muted">Your Kingdom</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $kingdoms->appends(request()->query())->links() }}
                </div>

                <!-- Ranking Legend -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="game-card">
                            <div class="card-header">
                                <h6 class="mb-0">Top Players</h6>
                            </div>
                            <div class="card-body">
                                @foreach($kingdoms->take(3) as $index => $kingdom)
                                    <div class="top-player d-flex align-items-center mb-3 p-2 border rounded">
                                        <div class="rank-badge rank-{{ $index + 1 }} me-3">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong>{{ $kingdom->name }}</strong>
                                            <div class="small text-muted">
                                                {{ $kingdom->user->username }} • {{ $kingdom->tribe->name }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">{{ number_format($kingdom->total_attack_power) }}</div>
                                            <small class="text-muted">Power</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="game-card">
                            <div class="card-header">
                                <h6 class="mb-0">Your Position</h6>
                            </div>
                            <div class="card-body text-center">
                                @php
                                    $userKingdom = auth()->user()->kingdom;
                                    $userRank = $kingdoms->search(function($item) use ($userKingdom) {
                                        return $item->id == $userKingdom->id;
                                    });
                                    $userRank = $userRank !== false ? $userRank + 1 + (($kingdoms->currentPage() - 1) * $kingdoms->perPage()) : 'N/A';
                                @endphp
                                
                                <div class="your-rank">
                                    <div class="rank-badge rank-{{ $userRank <= 3 ? $userRank : 'other' }} large">
                                        @if($userRank <= 3)
                                            {{ $userRank }}
                                        @else
                                            {{ $userRank }}<small>th</small>
                                        @endif
                                    </div>
                                    <h4 class="mt-3">{{ $userKingdom->name }}</h4>
                                    <p class="text-muted">Global Ranking</p>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <strong>{{ number_format($userKingdom->total_attack_power) }}</strong>
                                            <div class="small text-muted">Attack</div>
                                        </div>
                                        <div class="col-6">
                                            <strong>{{ number_format($userKingdom->total_defense_power) }}</strong>
                                            <div class="small text-muted">Defense</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div style="font-size: 4rem;">🏰</div>
                    <h4 class="mt-3">No Kingdoms Found</h4>
                    <p class="text-muted">There are no kingdoms matching your search criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    
.ranking-table,
.ranking-table th,
.ranking-table td,
.ranking-table thead th,
.ranking-table tbody td {
    color: #ffffff !important;
}

.ranking-table,
.ranking-table th,
.ranking-table td {
    border-color: #ffffff !important;
}

.rank-badge {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}

.rank-badge.large {
    width: 80px;
    height: 80px;
    font-size: 2rem;
    margin: 0 auto;
}

.rank-badge.rank-1 {
    background: linear-gradient(135deg, #FFD700, #FFA500);
}

.rank-badge.rank-2 {
    background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
}

.rank-badge.rank-3 {
    background: linear-gradient(135deg, #CD7F32, #8B4513);
}

.rank-badge.rank-other {
    background: linear-gradient(135deg, #6c757d, #495057);
}

.rank-number {
    text-align: center;
    font-weight: bold;
    color: #6c757d;
}

.power-value {
    min-width: 100px;
}

.troop-count, .gold-count {
    font-weight: 600;
}

.top-player {
    background: rgba(52, 152, 219, 0.1);
    transition: all 0.3s ease;
}

.top-player:hover {
    background: rgba(52, 152, 219, 0.2);
    transform: translateX(5px);
}

.table-active {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(41, 128, 185, 0.1)) !important;
}
</style>
@endsection