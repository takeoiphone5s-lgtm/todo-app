<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete'])
    ->name('tasks.complete');

Route::patch('/tasks/{id}/undo', [TaskController::class, 'undo'])->name('tasks.undo');

Route::resource('tasks', TaskController::class)
    ->only(['index','store']);