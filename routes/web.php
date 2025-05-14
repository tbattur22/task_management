<?php

use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\Tasks\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    /** Projects */
    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects');
    Route::post('/projects/create', [ProjectsController::class, 'create'])->name('project.create');
    Route::post('/projects', [ProjectsController::class, 'store'])->name('project.store');
    Route::get('/projects/{id}', [ProjectsController::class, 'edit'])->name('project.edit');
    Route::put('/projects/{id}', [ProjectsController::class, 'update'])->name('project.update');
    Route::delete('/projects/{id}', [ProjectsController::class, 'destroy'])->name('project.destroy');


    /** Tasks */
    Route::get('/', [TasksController::class, 'index'])->name('home');
    Route::post('/tasks/create/{projectId}', [TasksController::class, 'create'])->name('task.create');
    Route::post('/tasks', [TasksController::class, 'store'])->name('task.store');
    Route::get('/tasks/{id}', [TasksController::class, 'edit'])->name('task.edit');
    Route::put('/tasks/{task}', [TasksController::class, 'update'])->name('task.update');
    Route::delete('/tasks/{id}', [TasksController::class, 'destroy'])->name('task.destroy');

    Route::post('/tasks/select_project/{id}', [TasksController::class, 'selectProject'])->name('project.select');

    Route::post('/priority', [TasksController::class, 'updatePriority'])->name('task.updatePriority');

    Route::fallback(function () {
        return Redirect::route('home');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
