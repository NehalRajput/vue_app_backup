
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ExpenseController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Group routes
    Route::apiResource('groups', GroupController::class);

    // Expense routes
    Route::get('/expenses/export', [ExpenseController::class, 'export']); 
    Route::get('/expenses/export-pdf', [ExpenseController::class, 'exportPdf']);
    Route::apiResource('expenses', ExpenseController::class);
    


    // Logout route
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
