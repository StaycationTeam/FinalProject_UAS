<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\KingdomController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| PLAYER AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PLAYER GAME (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [GameController::class, 'dashboard'])->name('game.dashboard');
    Route::get('/rankings', [GameController::class, 'rankings'])->name('game.rankings');
    Route::get('/troops', [GameController::class, 'troops'])->name('game.troops');

    Route::get('/buildings', [KingdomController::class, 'showBuildings'])->name('kingdom.buildings');
    Route::post('/build-barracks', [KingdomController::class, 'buildBarracks'])->name('kingdom.build.barracks');
    Route::post('/build-mine', [KingdomController::class, 'buildMine'])->name('kingdom.build.mine');
    Route::post('/build-walls', [KingdomController::class, 'buildWalls'])->name('kingdom.build.walls');
    Route::post('/upgrade-main', [KingdomController::class, 'upgradeMainBuilding'])->name('kingdom.upgrade.main');

    Route::get('/battle', [BattleController::class, 'showBattle'])->name('game.battle');
    Route::post('/attack', [BattleController::class, 'attack'])->name('game.attack');

    Route::get('/training', [BattleController::class, 'showTraining'])
        ->name('game.training');

    Route::post('/training/attack', [BattleController::class, 'trainingAttack'])
        ->name('game.training.attack');
});

/*
|--------------------------------------------------------------------------
| ADMIN AUTH (TERPISAH)
|--------------------------------------------------------------------------
*/
Route::middleware('web')->prefix('admin')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL (PROTECTED)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/buildings', [AdminController::class, 'buildingSettings'])
            ->name('admin.buildings');

        Route::put('/buildings/{id}', [AdminController::class, 'updateBuilding'])
            ->name('admin.buildings.update');

        Route::get('/tribes', [AdminController::class, 'tribes'])
            ->name('admin.tribes');

        Route::put('/tribes/{id}', [AdminController::class, 'updateTribe'])
            ->name('admin.tribes.update');
    });
});

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (session()->has('is_admin')) {
        return redirect('/admin/dashboard');
    }

    return auth()->check()
        ? redirect('/dashboard')
        : redirect('/login');
});

