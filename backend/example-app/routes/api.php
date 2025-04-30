
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

use App\Http\Controllers\ExpenseController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  
    Route::apiResource('groups', GroupController::class);
   Route::apiResource('expenses', ExpenseController::class);

    Route::delete('/logout',[AuthController::class, 'logout'])->name('logout');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Add more protected routes here as needed
});

//Route::apiResource('groups', groupcontroller::class);
//Route::apiResource('expenses', expensecontroller::class);









