<?php

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

Route::middleware(['auth', 'verified'])->group(function(){

    Route::prefix('task')->group(function(){
        
       Route::get('/', [TaskController::class, 'index'])->name('task.index');

       Route::post('/', [TaskController::class, 'store'])->name('task.store');
    });

});

require __DIR__.'/settings.php';
