<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\KingdomController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Auth\RegisterController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Game Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [GameController::class, 'dashboard'])->name('game.dashboard');
    Route::get('/rankings', [GameController::class, 'rankings'])->name('game.rankings');
    
    // Troops
    Route::get('/troops', [GameController::class, 'troops'])->name('game.troops');

    // Kingdom Management
    Route::get('/buildings', [KingdomController::class, 'showBuildings'])->name('kingdom.buildings');
    Route::post('/build-barracks', [KingdomController::class, 'buildBarracks'])->name('kingdom.build.barracks');
    Route::post('/build-mine', [KingdomController::class, 'buildMine'])->name('kingdom.build.mine');
    Route::post('/build-walls', [KingdomController::class, 'buildWalls'])->name('kingdom.build.walls');
    Route::post('/upgrade-main', [KingdomController::class, 'upgradeMainBuilding'])->name('kingdom.upgrade.main');
    
    // Battle System
    Route::get('/battle', [BattleController::class, 'showBattle'])->name('game.battle');
    Route::post('/attack', [BattleController::class, 'attack'])->name('game.attack');
    
    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/tribes', [AdminController::class, 'tribeSettings'])->name('admin.tribes');
        Route::post('/tribes/{id}', [AdminController::class, 'updateTribe'])->name('admin.tribes.update');
        Route::get('/buildings', [AdminController::class, 'buildingSettings'])->name('admin.buildings');
        Route::post('/buildings/{id}', [AdminController::class, 'updateBuilding'])->name('admin.buildings.update');
    });
});

Route::prefix('admin')->group(function () {

    // Login admin
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('admin.login');

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

    // Protected admin area
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('admin.dashboard');

        Route::get('/buildings', [AdminController::class, 'buildingSettings']);
        Route::put('/buildings/{id}', [AdminController::class, 'updateBuilding']);
    });
});

// Redirect root to dashboard if authenticated
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});