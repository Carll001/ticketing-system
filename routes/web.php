<?php

use App\Http\Controllers\DepartmentController;
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
        
       Route::get('/', [TaskController::class, 'index'])->name('task.index');
       Route::get('/create', [TaskController::class, 'create'])->name('task.create');
       Route::get('/{task}', [TaskController::class, 'show'])->name('task.show');
       Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');

       Route::post('/', [TaskController::class, 'store'])->name('task.store');
       Route::put('/{task}',  [TaskController::class, 'update'])->name('task.update');
       Route::delete('/{task}', [TaskController::class, 'destroy'])->name('task.delete');
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
