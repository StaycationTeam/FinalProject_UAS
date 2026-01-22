@extends('layouts.app')

@section('title', 'Battle Arena - Warlord Rising')

@section('content')
<div class="container">

    {{-- Battle Result --}}
    @if(session('battle_result'))
        @php $result = session('battle_result'); @endphp
        <div class="alert {{ $result['result'] == 'win' ? 'alert-success' : 'alert-danger' }} alert-game mb-4">
            <h4 class="mb-0"><i class="fas {{ $result['result'] == 'win' ? 'fa-trophy' : 'fa-skull-crossbones' }} me-2"></i>
                Battle {{ $result['result'] == 'win' ? 'Victory!' : 'Defeat!' }}
            </h4>
            <p class="mb-0 mt-2">
                Gold {{ $result['result'] == 'win' ? 'Stolen: +' . number_format($result['gold_stolen']) : 'Lost: 0' }}
            </p>
        </div>

        <div class="game-card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-scroll me-2"></i> Battle Log</h5>
            </div>
            <div class="card-body">
                <pre style="white-space: pre-wrap; font-family: var(--font-mono); color: var(--text-primary); background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 4px;">
{{ $result['log'] }}</pre>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-game mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-game mb-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif


    <div class="row">
        {{-- Your Kingdom --}}
        <div class="col-md-4 mb-4">
            <div class="game-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-shield me-2 text-blue"></i> Your Forces</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-crown text-gold fa-3x mb-3"></i>
                        <h4 class="fw-bold">{{ $userKingdom->name }}</h4>

                        <div class="d-inline-block px-3 py-1 rounded border border-primary text-primary mb-3" style="font-size: 0.9rem;">
                            {{ $userKingdom->tribe->name }}
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">TROOPS</div>
                                <div class="h4 mb-0 text-blue font-mono">{{ number_format($userKingdom->total_troops) }}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">ATTACK</div>
                                <div class="h5 mb-0 text-danger font-mono">{{ number_format($userKingdom->total_attack_power) }}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">DEFENSE</div>
                                <div class="h5 mb-0 text-success font-mono">{{ number_format($userKingdom->total_defense_power) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Battle Interface --}}
        <div class="col-md-4 mb-4">
            <div class="game-card h-100">
                <div class="card-header text-center">
                    <h5 class="mb-0"><i class="fas fa-swords me-2 text-danger"></i> Launch Attack</h5>
                </div>
                <div class="card-body text-center d-flex flex-column">
                    <div style="font-size:4rem;margin-bottom:1rem;">⚔️</div>
                    <p class="text-secondary">Select a target kingdom to raid</p>

                    @if($userKingdom->total_troops < 1)
                        <div class="alert alert-warning alert-game mt-auto">
                            <i class="fas fa-exclamation-triangle me-2"></i> You need at least 1 troop to attack!
                        </div>

                    @elseif($targetKingdoms->count() > 0)
                        <form method="POST" action="{{ route('game.attack') }}" id="attackForm" class="mt-auto">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-secondary text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Select Target:</label>
                                <select class="form-select" name="defender_id" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--border-color); color: var(--text-primary);">
                                    <option value="">Choose a kingdom...</option>

                                    @foreach($targetKingdoms as $target)
                                        <option value="{{ $target->id }}">
                                            {{ $target->name }}
                                            ({{ $target->user->username }})
                                            - Defense: {{ number_format($target->total_defense_power) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-game danger btn-lg w-100">
                                <i class="fas fa-skull-crossbones me-2"></i> LAUNCH ATTACK!
                            </button>
                        </form>

                    @else
                        <div class="alert alert-game mt-auto" style="border-color: var(--accent-primary); color: var(--accent-primary);">
                            <i class="fas fa-info-circle me-2"></i>
                            No available targets. Other kingdoms need troops/resources first.
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- Available Targets --}}
        <div class="col-md-4 mb-4">
            <div class="game-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-crosshairs me-2 text-danger"></i> Available Targets</h5>
                </div>

                <div class="card-body" style="max-height: 400px; overflow-y:auto;">
                    @if($targetKingdoms->count() > 0)
                        @foreach($targetKingdoms as $target)
                            <div class="mb-3 p-3 rounded" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent-danger)'" onmouseout="this.style.borderColor='var(--border-color)'">
                                <h6 class="mb-2 fw-bold">{{ $target->name }}</h6>

                                <div class="d-inline-block px-2 py-1 rounded border border-secondary text-secondary mb-2" style="font-size: 0.75rem;">
                                    {{ $target->tribe->name }}
                                </div>

                                <div class="small text-secondary">
                                    <div><i class="fas fa-user me-1"></i> {{ $target->user->username }}</div>
                                    <div><i class="fas fa-shield me-1 text-success"></i> Defense: {{ number_format($target->total_defense_power) }}</div>
                                    <div><i class="fas fa-users me-1 text-blue"></i> Troops: {{ number_format($target->total_troops) }}</div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-peace text-secondary fa-3x mb-3"></i>
                            <p class="text-secondary">No available targets</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



    {{-- BATTLE HISTORY --}}
    <div class="game-card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i> Battle History</h5>
        </div>

        <div class="card-body">
            @if($battleHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-secondary text-uppercase" style="font-size: 0.8rem; border-color: var(--border-color);">
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

                                <tr style="border-color: var(--border-color);">
                                    {{-- Battle Type --}}
                                    <td class="align-middle">
                                        @if($isAttacker)
                                            <span class="text-danger"><i class="fas fa-arrow-right me-1"></i> You attacked</span><br>
                                            <small class="text-secondary">{{ $opponentName }}</small>
                                        @else
                                            <span class="text-info"><i class="fas fa-arrow-left me-1"></i> You were attacked</span><br>
                                            <small class="text-secondary">by {{ $opponentName }}</small>
                                        @endif
                                    </td>

                                    {{-- Opponent --}}
                                    <td class="align-middle">{{ $opponentUser }}</td>

                                    {{-- Result --}}
                                    <td class="align-middle">
                                        @if($isAttacker)
                                            <span class="badge {{ $battle->result == 'win' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $battle->result == 'win' ? 'VICTORY' : 'DEFEAT' }}
                                            </span>
                                        @else
                                            <span class="badge {{ $battle->result == 'win' ? 'bg-danger' : 'bg-success' }}">
                                                {{ $battle->result == 'win' ? 'RAID FAILED' : 'DEFENDED' }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Gold --}}
                                    <td class="align-middle font-mono">
                                        @if($isAttacker && $battle->result == 'win')
                                            <span class="text-gold">+{{ number_format($battle->gold_stolen) }}</span>

                                        @elseif(!$isAttacker && $battle->result == 'win')
                                            <span class="text-danger">-{{ number_format($battle->gold_stolen) }}</span>

                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td class="align-middle text-secondary small">{{ $battle->created_at->diffForHumans() }}</td>
                                </tr>

                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="fas fa-peace text-secondary fa-3x mb-3"></i>
                    <p class="text-secondary">No battle history found. Start your conquest!</p>
                    <a href="#attackForm" class="btn btn-outline-primary btn-sm">Launch First Attack</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
