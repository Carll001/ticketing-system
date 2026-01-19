<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\StepController;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

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
    });
});

require __DIR__ . '/settings.php';
