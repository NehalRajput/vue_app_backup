
<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

use App\Http\Controllers\ExpenseController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('expenses', ExpenseController::class);

    Route::get('/expenses/export', [ExpenseController::class, 'export']); // ✅ Move here

    Route::delete('/logout',[AuthController::class, 'logout'])->name('logout');
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
//Route::get('/expenses/export', [ExpenseController::class, 'export']);



