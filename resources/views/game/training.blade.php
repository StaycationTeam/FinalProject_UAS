@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Training Mode</h2>
    <p>Fight against AI. No gold reward. Just pure pain.</p>

    @if(session('battle_result'))
        <div class="alert alert-info">
            {{ session('battle_result')['log'] }}
        </div>
    @endif

    <table class="table table-dark">
        <thead>
            <tr>
                <th>Enemy</th>
                <th>Tribe</th>
                <th>Troops</th>
                <th>Defense</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($aiTargets as $enemy)
            <tr>
                <td>{{ $enemy->name }}</td>
                <td>{{ $enemy->tribe->name }}</td>
                <td>{{ $enemy->total_troops }}</td>
                <td>{{ $enemy->total_defense_power }}</td>
                <td>
                    <form method="POST" action="{{ route('game.training.attack') }}">
                        @csrf
                        <input type="hidden" name="defender_id" value="{{ $enemy->id }}">
                        <button class="btn btn-warning btn-sm">
                            Attack
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr class="my-4">

    <h4>Training History</h4>

    @if($trainingHistory->count())
        <div class="table-responsive">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Opponent</th>
                        <th>Result</th>
                        <th>Power (You vs AI)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainingHistory as $battle)
                        <tr>
                            <td>{{ $battle->battle_log }}</td>

                            <td>
                                @if($battle->result === 'win')
                                    <span class="badge bg-success">Win</span>
                                @else
                                    <span class="badge bg-danger">Lose</span>
                                @endif
                            </td>

                            <td>
                                {{ $battle->attacker_power }}
                                vs
                                {{ $battle->defender_power }}
                            </td>

                            <td>{{ $battle->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted">No training history yet.</p>
    @endif


</div>
@endsection
