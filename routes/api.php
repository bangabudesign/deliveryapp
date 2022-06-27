<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UpdateLocation;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

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

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan data user',
            'data' => $request->user()
        ];

        return response()->json($response, Response::HTTP_OK);
    });
    Route::get('/slides', function (Request $request) {
        $slides = [
            [
                'id' => 1,
                'image' => '/images/slides/1.jpeg',
                'title' => 'Slide 1',
            ],
            [
                'id' => 2,
                'image' => '/images/slides/2.jpeg',
                'title' => 'Slide 2',
            ],
            [
                'id' => 3,
                'image' => '/images/slides/3.jpeg',
                'title' => 'Slide 3',
            ]
        ];
        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan data user',
            'data' => $slides
        ];

        return response()->json($response, Response::HTTP_OK);
    });
    Route::post('/update-location', [UpdateLocation::class, 'update']);
    Route::resource('/restaurants', RestaurantController::class)->only('index', 'show', 'store', 'update');
    Route::resource('/carts', CartController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('/drivers', DriverController::class)->only('index');
    Route::resource('/orders', OrderController::class)->only('index', 'store', 'update', 'destroy');
});
