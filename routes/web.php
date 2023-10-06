<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProductImagesController;
use App\Http\Controllers\admin\ProductsController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\ProductSubCategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');

Route::group(['prefix' => '/admin'], function(){

    Route::group(['middleware' => 'admin.guest'], function(){

        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware' => 'admin.auth'], function(){

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        //category route
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edite', [CategoryController::class, 'edite'])->name('categories.edite');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');

        //sub category route
        Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
        Route::get('/subcategories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
        Route::post('/subcategories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
        Route::get('/subcategories/{subCategory}/edite', [SubCategoryController::class, 'edite'])->name('sub-categories.edite');
        Route::put('/subcategories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
        Route::delete('/subcategories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');

         //brand route
         Route::get('/brands', [BrandsController::class, 'index'])->name('brands.index');
         Route::get('/brands/create', [BrandsController::class, 'create'])->name('brands.create');
         Route::post('/brands', [BrandsController::class, 'store'])->name('brands.store');
         Route::get('/brands/{brand}/edite', [BrandsController::class, 'edite'])->name('brands.edite');
         Route::put('/brands/{brand}', [BrandsController::class, 'update'])->name('brands.update');
         Route::delete('/brands/{brand}', [BrandsController::class, 'destroy'])->name('brands.delete');

          //product route
          Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
          Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
          Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
          Route::get('/products/{product}/edite', [ProductsController::class, 'edite'])->name('products.edite');
          Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
          Route::delete('/products/{product}', [ProductsController::class, 'destroy'])->name('products.delete');




        // Product sub category
        Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');

        // temp-images.create
        Route::post('/temp', [TempImagesController::class, 'create'])->name('temp-images.create');

         // product-images from productImagesController
        Route::post('/product-images/update', [ProductImagesController::class, 'update'])->name('product-images.update');
        Route::delete('/product-images', [ProductImagesController::class, 'destroy'])->name('product-images.delete');

        Route::get('/getSlug',function(Request $request){
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }

            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');

        // Route for storing the file input content in the session
        Route::post('/store-file-content', 'Categories@storeFileContent')->name('storeFileContent');

          // Route for auto-complete functionality
        Route::get('/autocomplete', 'Categories@autocomplete')->name('autocomplete');
    });
});


