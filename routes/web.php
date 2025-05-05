<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\Tasks\TasksController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [TasksController::class, 'index'])->name('home');

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects');

    Route::get('/tasks/select_project/{id}', [TasksController::class, 'index'])->name('home');

    Route::post('/tasks/select_project/{id}', [TasksController::class, 'selectProject'])->name('project.select');

    Route::post('/tasks/create', [TasksController::class, 'create'])->name('task.create');

    Route::get('/tasks', [TasksController::class, 'index'])->name('home');

    Route::post('/tasks', [TasksController::class, 'store'])->name('task.store');


    Route::get('/tasks/{id}', [TasksController::class, 'edit'])->name('task.edit');

    Route::put('/tasks/{id}', [TasksController::class, 'update'])->name('task.update');

    Route::delete('/tasks/{id}', [TasksController::class, 'destroy'])->name('task.destroy');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
