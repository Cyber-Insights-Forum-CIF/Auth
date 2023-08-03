<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ArticleApiController;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Support\Carbon;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix("v1")->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::apiResource('category',CategoryApiController::class);
        Route::apiResource('user',UserApiController::class)->middleware('admin');
        Route::post("logout", [ApiAuthController::class, 'logout']);
        Route::post("logout-all", [ApiAuthController::class, 'logoutAll']);
        Route::get("devices", [ApiAuthController::class, 'devices']);
        Route::get("article/created", [ArticleApiController::class, 'showCreated']);


        Route::prefix("trash")->group(function(){


            Route::prefix('article')->group(function(){
                Route::get("/", [ArticleApiController::class, 'trash'])->withTrashed();
                Route::delete("/{id}",[ArticleApiController::class,"forceDelete"])->withTrashed();
                Route::put("/{id}/restore",[ArticleApiController::class, 'restore'])->withTrashed();
            });


        })->middleware('auth');


    });

    Route::apiResource('article',ArticleApiController::class)->middleware('throttle:60,1');


    Route::post("register", [ApiAuthController::class, 'register']);
    Route::post("login", [ApiAuthController::class, 'login'])->name("login");
    Route::get('/time', function () {
        return response()->json(['server_time' => Carbon::now()->toDateTimeString()], 200);
    });
});



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });






