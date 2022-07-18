<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')
    ->group(static function () {
        Route::prefix('products/')
            ->group(static function () {
                Route::get('{integration}/{externalId}', [ProductsController::class, 'getProduct']);
            });

        Route::prefix('cart/')
            ->group(static function () {
                Route::prefix('products/')
                    ->group(static function () {
                        Route::get('', [CartsController::class, 'getProducts']);
                        Route::post('', [CartsController::class, 'addProduct']);
                        Route::delete('/{externalId}', [CartsController::class, 'deleteProduct']);
                    });

                Route::post('checkout', [CartsController::class, 'checkout']);
            });
    });

Route::prefix('auth/')
    ->group(static function () {

        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
