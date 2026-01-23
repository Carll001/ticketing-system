<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\StepController;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProofController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {


    Route::prefix('task')->group(function(){
        
    // TASK 
        Route::get('/', [TaskController::class, 'index'])->name('task.index');
        Route::get('/create', [TaskController::class, 'create'])->name('task.create');
        Route::get('/{task}', [TaskController::class, 'show'])->name('task.show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');

        Route::post('/', [TaskController::class, 'store'])->name('task.store');
        Route::patch('/{task}',  [TaskController::class, 'update'])->name('task.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('task.delete');

    // STEP
        Route::get('{task}/step/create', [StepController::class, 'create'])->name('step.create'); 
        Route::get('{task}/step/{step}', [StepController::class, 'show'])->name('step.show');
        Route::get('/{task}/step/{step}/edit', [StepController::class, 'edit'])->name('step.edit');

        Route::post('/{task}/step', [StepController::class, 'store'])->name('step.store');
        Route::patch('/{task}/step/{step}', [StepController::class, 'update'])->name('step.update');
        Route::delete('/{task}/step/{step}', [StepController::class, 'destroy'])->name('step.delete');
    });

    //department routes
    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
        Route::post('/', [DepartmentController::class, 'store']) ->name('department.store');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('department.update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('department.show');
    });


    Route::prefix('user')->group(function(){
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('user.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('response')->group(function() {
        Route::post('/', [ResponseController::class, 'store'])->name('response.store');
    });

     // PROOF
    Route::prefix('{task}/step/{step}/proof')->group(function () {
        Route::get('/', [ProofController::class, 'index'])->name('proof.index'); // list proofs for this step
        Route::get('/create', [ProofController::class, 'create'])->name('proof.create');
        Route::get('/{proof}', [ProofController::class, 'show'])->name('proof.show');
        Route::get('/{proof}/edit', [ProofController::class, 'edit'])->name('proof.edit');

        Route::post('/', [ProofController::class, 'store'])->name('proof.store');
        Route::patch('/{proof}', [ProofController::class, 'update'])->name('proof.update');
        Route::delete('/{proof}', [ProofController::class, 'destroy'])->name('proof.delete');
    });

});

require __DIR__ . '/settings.php';
