@extends('layouts.app')

@section('title', 'Buildings - Strategy Game')

@section('content')
<div class="container">
    <div class="game-card">
        <div class="card-header">
            <h5 class="mb-0">Kingdom Buildings</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-game">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-game">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <!-- Main Building -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="building-card">
                        <div class="building-icon">🏰</div>
                        <h5>Castle Kingdom</h5>
                        <p class="text-muted">Your kingdom's central structure</p>
                        <div class="mb-3">
                            <strong>Level: {{ $kingdom->main_building_level }}</strong>
                        </div>
                        <form method="POST" action="{{ route('kingdom.upgrade.main') }}">
                            @csrf
                            <button type="submit" class="btn btn-game">
                                Upgrade ({{ $buildings->where('type', 'main')->first()->gold_cost * $kingdom->main_building_level }} Gold)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Barracks -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="building-card">
                        <div class="building-icon">⚔️</div>
                        <h5>Barracks</h5>
                        <p class="text-muted">Produces troops for your army</p>
                        <div class="mb-3">
                            <strong>Count: {{ $kingdom->barracks_count }}</strong><br>
                            <small>+5 troops/min each</small>
                        </div>
                        <form method="POST" action="{{ route('kingdom.build.barracks') }}">
                            @csrf
                            <button type="submit" class="btn btn-game">
                                Build ({{ $buildings->where('type', 'barracks')->first()->gold_cost }} Gold)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mine -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="building-card">
                        <div class="building-icon">⛏️</div>
                        <h5>Gold Mine</h5>
                        <p class="text-muted">Increases gold production</p>
                        <div class="mb-3">
                            <strong>Count: {{ $kingdom->mines_count }}</strong><br>
                            <small>+10 gold/min each</small>
                        </div>
                        <form method="POST" action="{{ route('kingdom.build.mine') }}">
                            @csrf
                            <button type="submit" class="btn btn-game gold">
                                Build ({{ $buildings->where('type', 'mine')->first()->gold_cost }} Gold)
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Walls -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="building-card">
                        <div class="building-icon">🛡️</div>
                        <h5>Defense Walls</h5>
                        <p class="text-muted">Increases kingdom defense</p>
                        <div class="mb-3">
                            <strong>Count: {{ $kingdom->walls_count }}</strong><br>
                            <small>+10 defense each</small>
                        </div>
                        <form method="POST" action="{{ route('kingdom.build.walls') }}">
                            @csrf
                            <button type="submit" class="btn btn-game success">
                                Build ({{ $buildings->where('type', 'walls')->first()->gold_cost }} Gold)
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Production Summary -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="game-card">
                        <div class="card-header">
                            <h6 class="mb-0">Production Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Gold Production</h6>
                                    <p>Base: 5 gold/min</p>
                                    <p>From Mines: {{ $kingdom->mines_count * 10 }} gold/min</p>
                                    <p><strong>Total: {{ 5 + ($kingdom->mines_count * 10) }} gold/min</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Troop Production</h6>
                                    <p>Base: {{ $kingdom->tribe->troop_production_rate }} troops/min</p>
                                    <p>From Barracks: {{ $kingdom->barracks_count * 5 }} troops/min</p>
                                    <p><strong>Total: {{ $kingdom->tribe->troop_production_rate + ($kingdom->barracks_count * 5) }} troops/min</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection