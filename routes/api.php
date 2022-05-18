<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\VarianceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\CartController;


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
 
    'prefix' => 'auth'
], function () {

    Route::post('login', [AdminController::class, 'login']);
    Route::post('logout', [AdminController::class, 'show']);
    Route::post('refresh', [AdminController::class, 'show']);
    Route::post('me', [AdminController::class, 'show']);
    
    Route::group([
        'middleware' => 'api'
    ], function() {
        Route::post('createCategory', [CategoryController::class, 'createCategory']);
        Route::get('showallCategory', [CategoryController::class, 'showallCategory']);
        Route::post('showCategoryById', [CategoryController::class, 'showCategoryById']);
        Route::patch('editCategory', [CategoryController::class, 'editCategory']);
        Route::post('deleteCategory', [CategoryController::class, 'deleteCategory']);

        Route::post('createSubCategory', [SubcategoryController::class, 'createSubCategory']);
        Route::get('showallSubCategory', [SubcategoryController::class, 'showallSubCategory']);
        Route::post('showSubCategoryById', [SubcategoryController::class, 'showSubCategoryById']);
        Route::patch('editsubCategory', [SubcategoryController::class, 'editsubCategory']);
        Route::post('deleteSubCategory', [SubcategoryController::class, 'deleteSubCategory']);


        Route::post('createProduct', [ProductController::class, 'createProduct']);
        Route::get('showallProduct', [ProductController::class, 'showallProduct']);
        Route::get('showallDeactiveProduct', [ProductController::class, 'showallDeactiveProduct']);
        Route::post('showProductById', [ProductController::class, 'showProductById']);
        Route::patch('editProduct', [ProductController::class, 'editProduct']);
        Route::post('deleteProduct', [ProductController::class, 'deleteProduct']);
        Route::post('deleteMultipleProducts', [ProductController::class, 'deleteMultipleProducts']);
        Route::post('uplodImagesTogallery', [ProductController::class, 'uplodImagesTogallery']);
        Route::get('getImagesFromgallery', [ProductController::class, 'getImagesFromgallery']);
        Route::post('activatedeactivateProduct', [ProductController::class, 'activatedeactivateProduct']);
        Route::post('showProductsByAdmin', [ProductController::class, 'showProductsByAdmin']);
        Route::get('showProductstoUsers', [ProductController::class, 'showProductstoUsers']);
        Route::get('showProductstoAdmin', [ProductController::class, 'showProductstoAdmin']);


        Route::post('createAttribute', [AttributeController::class, 'createAttribute']);
        Route::post('editAttribute', [AttributeController::class, 'editAttribute']);
        Route::post('deleteAttribute', [AttributeController::class, 'deleteAttribute']);
        // Route::post('editAttribute', [AttributeController::class, 'editAttribute']);

        Route::post('createVariance', [VarianceController::class, 'createVariance']);
        Route::post('editVariance', [VarianceController::class, 'editVariance']);
        Route::post('deleteVariance', [VarianceController::class, 'deleteVariance']);



        Route::post('addToCart', [CartController::class, 'addToCart']);


    });
});