<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\UpdateLocation;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\ProfitSharingController;
use App\Http\Controllers\Api\WithdrawalController;

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
    Route::post('/update-status', [DriverController::class, 'updateStatus']);
    Route::resource('/banners', BannerController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('/restaurants', RestaurantController::class)->only('index', 'show', 'store', 'update');
    Route::resource('/products', ProductController::class)->only('store', 'update');
    Route::resource('/carts', CartController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('/orders', OrderController::class)->only('index', 'store', 'update', 'destroy');
    Route::resource('/drivers', DriverController::class)->only('index', 'store', 'update');
    Route::resource('/merchants', MerchantController::class)->only('index', 'store', 'update');
    Route::resource('/users', UserController::class)->only('index', 'store', 'update');
    Route::resource('/images', ImageController::class)->only('index', 'store', 'destroy');
    Route::resource('/deposits', DepositController::class)->only('index', 'store', 'show', 'update');
    Route::resource('/withdrawals', WithdrawalController::class)->only('index', 'store', 'show', 'update');
    Route::post('/deposits/receipt/{id}', [DepositController::class, 'uploadReceipt']);
    Route::resource('/transactions', TransactionController::class)->only('index', 'store', 'update');
    Route::resource('/bonuses', BonusController::class)->only('index', 'store', 'update');
    Route::resource('/sharings', ProfitSharingController::class)->only('index', 'store', 'update');
    Route::get('/sharings/stats', [ProfitSharingController::class, 'stats']);
    Route::post('/sharings/send', [ProfitSharingController::class, 'sendProfit']);
    Route::resource('/banks', BankController::class)->only('index', 'store', 'update');
});
