@extends('layouts.app')

@section('title', 'Training Arena - Warlord Rising')

@section('content')
<div class="container">
    
    @if(session('info'))
        <div class="alert alert-info alert-game mb-4">
            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-game mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Your Kingdom Stats -->
        <div class="col-md-4 mb-4">
            <div class="game-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-shield me-2 text-blue"></i> Your Forces</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-crown text-gold fa-3x mb-3"></i>
                        <h4 class="fw-bold">{{ $kingdom->name }}</h4>
                        <div class="d-inline-block px-3 py-1 rounded border border-primary text-primary mb-3" style="font-size: 0.9rem;">
                            {{ $kingdom->tribe->name }}
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">TROOPS</div>
                                <div class="h4 mb-0 text-blue font-mono">{{ number_format($kingdom->total_troops) }}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">ATTACK</div>
                                <div class="h5 mb-0 text-danger font-mono">{{ number_format($kingdom->total_attack_power) }}</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.75rem; border-radius: 6px;">
                                <div class="text-secondary small">DEFENSE</div>
                                <div class="h5 mb-0 text-success font-mono">{{ number_format($kingdom->total_defense_power) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Training Info -->
        <div class="col-md-8 mb-4">
            <div class="game-card h-100">
                <div class="card-header text-center">
                    <h5 class="mb-0"><i class="fas fa-dumbbell me-2"></i> Training Arena</h5>
                </div>
                <div class="card-body text-center">
                    <div style="font-size:4rem;margin-bottom:1rem;">🎯</div>
                    <h4 class="fw-bold mb-3">Practice Your Battle Skills</h4>
                    <p class="text-secondary mb-4">
                        Fight against AI opponents to test your strategies.<br>
                        <span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> No gold rewards or troop losses</span><br>
                        Pure practice for strategy improvement!
                    </p>

                    <div class="row g-3 text-start">
                        <div class="col-md-6">
                            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); padding: 1rem; border-radius: 6px;">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>No Risk</strong><br>
                                <small class="text-secondary">Troops are safe - no casualties</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 1rem; border-radius: 6px;">
                                <i class="fas fa-graduation-cap text-blue me-2"></i>
                                <strong>Learn Strategy</strong><br>
                                <small class="text-secondary">Test different approaches safely</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Opponents -->
    <div class="game-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-robot me-2"></i> Available AI Opponents</h5>
        </div>
        <div class="card-body">
            @if($aiTargets->count() > 0)
                <div class="row">
                    @foreach($aiTargets as $enemy)
                        <div class="col-md-4 mb-3">
                            <div class="p-3 rounded" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--accent-primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
                                <h6 class="mb-2 fw-bold"><i class="fas fa-robot me-2 text-secondary"></i>{{ $enemy->name }}</h6>
                                
                                <div class="d-inline-block px-2 py-1 rounded border border-secondary text-secondary mb-2" style="font-size: 0.75rem;">
                                    {{ $enemy->tribe->name }}
                                </div>

                                <div class="small text-secondary mb-3">
                                    <div><i class="fas fa-shield me-1 text-success"></i> Defense: {{ number_format($enemy->total_defense_power) }}</div>
                                    <div><i class="fas fa-users me-1 text-blue"></i> Troops: {{ number_format($enemy->total_troops) }}</div>
                                </div>

                                <form method="POST" action="{{ route('game.training.attack') }}">
                                    @csrf
                                    <input type="hidden" name="defender_id" value="{{ $enemy->id }}">
                                    <button class="btn btn-game" style="background: #f59e0b; font-size: 0.85rem;">
                                        <i class="fas fa-crosshairs me-1"></i> Practice Attack
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-robot text-secondary fa-3x mb-3"></i>
                    <p class="text-secondary">No AI opponents available for training.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Training History -->
    <div class="game-card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i> Training History</h5>
        </div>
        <div class="card-body">
            @if($trainingHistory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="background: transparent;">
                        <thead>
                            <tr class="text-secondary text-uppercase" style="font-size: 0.8rem; border-color: var(--border-color);">
                                <th>Opponent</th>
                                <th>Result</th>
                                <th>Your Power</th>
                                <th>AI Power</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainingHistory as $battle)
                                @php
                                    $didWin = $battle->winner_id == $kingdom->id;
                                @endphp
                                <tr style="border-color: var(--border-color);">
                                    <td class="align-middle">
                                        <i class="fas fa-robot me-2 text-secondary"></i>
                                        {{ $battle->defender->name ?? 'AI Opponent' }}
                                    </td>
                                    <td class="align-middle">
                                        @if($didWin)
                                            <span class="badge bg-success">VICTORY</span>
                                        @else
                                            <span class="badge bg-danger">DEFEAT</span>
                                        @endif
                                    </td>
                                    <td class="align-middle font-mono text-danger">{{ $battle->attacker_troops }}</td>
                                    <td class="align-middle font-mono text-success">{{ $battle->defender_troops }}</td>
                                    <td class="align-middle text-secondary small">{{ $battle->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list text-secondary fa-3x mb-3"></i>
                    <p class="text-secondary">No training sessions yet. Start practicing!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
