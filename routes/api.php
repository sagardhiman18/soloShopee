<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AdminController::class, 'login']);
    Route::post('logout', [AdminController::class, 'show']);
    Route::post('refresh', [AdminController::class, 'show']);
    Route::post('me', [AdminController::class, 'show']);


    Route::post('createCategory', [CategoryController::class, 'createCategory']);
    Route::get('showallCategory', [CategoryController::class, 'showallCategory']);
    Route::post('showCategoryById', [CategoryController::class, 'showCategoryById']);
    Route::patch('editCategory', [CategoryController::class, 'editCategory']);
    // Route::post('deleteCategory', [CategoryController::class, 'deleteCategory']);

    Route::post('createSubCategory', [SubcategoryController::class, 'createSubCategory']);
    Route::get('showallSubCategory', [SubcategoryController::class, 'showallSubCategory']);
    Route::post('showSubCategoryById', [SubcategoryController::class, 'showSubCategoryById']);
    Route::patch('editsubCategory', [CategoryController::class, 'editsubCategory']);

    Route::post('createProduct', [CategoryController::class, 'createProduct']);
    Route::get('showallProduct', [CategoryController::class, 'showallProduct']);
    Route::post('showProductById', [CategoryController::class, 'showProductById']);
    Route::patch('editProduct', [CategoryController::class, 'editProduct']);

});