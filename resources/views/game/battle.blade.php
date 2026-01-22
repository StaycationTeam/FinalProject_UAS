@extends('layouts.app')

@section('title', 'Battle - Strategy Game')

@section('content')
<div class="container">

    {{-- Battle Result --}}
    @if(session('battle_result'))
        @php $result = session('battle_result'); @endphp
        <div class="battle-result {{ $result['result'] == 'win' ? 'battle-win' : 'battle-lose' }}">
            <h4 class="mb-0">Battle {{ $result['result'] == 'win' ? 'Victory!' : 'Defeat!' }}</h4>
            <p class="mb-0 mt-2">
                Gold {{ $result['result'] == 'win' ? 'Stolen: +' . $result['gold_stolen'] : 'Lost: 0' }}
            </p>
        </div>

        <div class="game-card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Battle Log</h5>
            </div>
            <div class="card-body">
                <pre style="white-space: pre-wrap; font-family: inherit;">
                    {{ $result['log'] }}
                </pre>
            </div>
        </div>
    @endif


    <div class="row">
        {{-- Your Kingdom --}}
        <div class="col-md-4">
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Your Forces</h5>
                </div>
                <div class="card-body">
                    <div class="attacker-card">
                        <h4>{{ $userKingdom->name }}</h4>

                        <div class="tribe-badge tribe-{{ strtolower($userKingdom->tribe->name) }} mb-3">
                            {{ $userKingdom->tribe->name }}
                        </div>

                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number">{{ number_format($userKingdom->total_troops) }}</div>
                                <div class="stat-label">Troops</div>
                            </div>

                            <div class="stat-card danger">
                                <div class="stat-number">{{ number_format($userKingdom->total_attack_power) }}</div>
                                <div class="stat-label">Attack Power</div>
                            </div>

                            <div class="stat-card success">
                                <div class="stat-number">{{ number_format($userKingdom->total_defense_power) }}</div>
                                <div class="stat-label">Defense Power</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Battle Interface --}}
        <div class="col-md-4">
            <div class="game-card text-center">
                <div class="card-header">
                    <h5 class="mb-0">Launch Attack</h5>
                </div>
                <div class="card-body">
                    <div style="font-size:4rem;margin-bottom:1rem;">⚔️</div>
                    <p class="text-muted">Select a target kingdom to attack</p>

                    @if($userKingdom->total_troops < 1)
                        <div class="alert alert-warning">You need at least 1 troop to attack!</div>

                    @elseif($targetKingdoms->count() > 0)
                        <form method="POST" action="{{ route('game.attack') }}" id="attackForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select Target:</label>
                                <select class="form-select" name="defender_id" required>
                                    <option value="">Choose a kingdom...</option>

                                    @foreach($targetKingdoms as $target)
                                        <option value="{{ $target->id }}">
                                            {{ $target->name }}
                                            ({{ $target->user->username }})
                                            - Power: {{ number_format($target->total_defense_power) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-game danger btn-lg">
                                Launch Attack!
                            </button>
                        </form>

                    @else
                        <div class="alert alert-info">
                            No available targets. Other kingdoms need troops/resources first.
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- Available Targets --}}
        <div class="col-md-4">
            <div class="game-card">
                <div class="card-header">
                    <h5 class="mb-0">Available Targets</h5>
                </div>

                <div class="card-body" style="max-height: 400px; overflow-y:auto;">
                    @if($targetKingdoms->count() > 0)
                        @foreach($targetKingdoms as $target)
                            <div class="defender-card mb-3">
                                <h6>{{ $target->name }}</h6>

                                <div class="tribe-badge tribe-{{ strtolower($target->tribe->name) }} mb-2">
                                    {{ $target->tribe->name }}
                                </div>

                                <div class="small">
                                    <div>Player: {{ $target->user->username }}</div>
                                    <div>Defense: {{ number_format($target->total_defense_power) }}</div>
                                    <div>Troops: {{ number_format($target->total_troops) }}</div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <p class="text-center text-muted">No available targets</p>
                    @endif
                </div>
            </div>
        </div>
    </div>



    {{-- BATTLE HISTORY --}}
<div class="game-card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Battle History</h5>
    </div>

    <div class="card-body">
        @if($battleHistory->count() > 0)
            <div class="table-responsive">
                <table class="table ranking-table">
                    <thead>
                        <tr>
                            <th>Battle</th>
                            <th>Opponent</th>
                            <th>Result</th>
                            <th>Gold</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($battleHistory as $battle)

                            @php
                                $isAttacker = $battle->attacker_id == $userKingdom->id;

                                $opponentKingdom = $isAttacker ? $battle->defender : $battle->attacker;

                                // Jika AI → defender/attacker null
                                $opponentName = $opponentKingdom ? $opponentKingdom->name : 'AI Opponent';
                                $opponentUser = $opponentKingdom && $opponentKingdom->user ? $opponentKingdom->user->username : 'AI';
                            @endphp

                            <tr>
                                {{-- Battle Type --}}
                                <td>
                                    @if($isAttacker)
                                        <span class="text-danger">You attacked</span><br>
                                        <small>{{ $opponentName }}</small>
                                    @else
                                        <span class="text-info">You were attacked</span><br>
                                        <small>by {{ $opponentName }}</small>
                                    @endif
                                </td>

                                {{-- Opponent --}}
                                <td>{{ $opponentUser }}</td>

                                {{-- Result --}}
                                <td>
                                    @if($isAttacker)
                                        <span class="badge {{ $battle->result == 'win' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($battle->result) }}
                                        </span>
                                    @else
                                        <span class="badge {{ $battle->result == 'win' ? 'bg-danger' : 'bg-success' }}">
                                            {{ $battle->result == 'win' ? 'Lost' : 'Won' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Gold --}}
                                <td>
                                    @if($isAttacker && $battle->result == 'win')
                                        <span class="text-success">+{{ $battle->gold_stolen }}</span>

                                    @elseif(!$isAttacker && $battle->result == 'win')
                                        <span class="text-danger">-{{ $battle->gold_stolen }}</span>

                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td>{{ $battle->created_at->format('d M Y H:i') }}</td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

        @else
            <p class="text-center text-muted">No battle history found.</p>
        @endif
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
</style>
@endsection
