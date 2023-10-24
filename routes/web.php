<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\DiscountCouponsController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\ProductImagesController;
use App\Http\Controllers\admin\ProductsController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\ShopController;
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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/test', function () {
    orderEmail(10);
});

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');


Route::group(['prefix' => '/auth'], function(){

    Route::group(['middleware' => 'guest'], function(){
        Route::get('/login',[AuthController::class,'login'])->name('auth.login');
        Route::post('/authenticate',[AuthController::class,'authenticate'])->name('auth.authenticate');
        Route::get('/register',[AuthController::class,'register'])->name('auth.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('auth.processRegister');

    });

    Route::group(['middleware' => 'auth'], function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/my-orders',[AuthController::class,'orders'])->name('account.myOrders');
        Route::get('/order-detial/{orderId}',[AuthController::class,'orderDetial'])->name('account.orderDetial');
        Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
        Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
        Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
        Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem');

        Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
        Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');
        Route::get('/thanks/{orderId}',[CartController::class,'thankYou'])->name('front.thankYou');
        Route::post('/getOrderSummary',[CartController::class,'getOrderSummary'])->name('front.getOrderSummary');
        Route::post('/applyDiscount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
        Route::post('/removeCoupon', [CartController::class, 'removeCoupon'])->name('front.removeCoupon');


    });
});


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
          Route::get('/get-products', [ProductsController::class, 'getProducts'])->name('products.getProducts');

           //shipping route
           Route::get('/shippings/create', [ShippingController::class, 'create'])->name('shippings.create');
           Route::post('/shippings', [ShippingController::class, 'store'])->name('shippings.store');
           Route::get('/shippings/{shipping}/edite', [ShippingController::class, 'edite'])->name('shippings.edite');
           Route::put('/shippings/{shipping}', [ShippingController::class, 'update'])->name('shippings.update');
           Route::delete('/shippings/{shippings}', [ShippingController::class, 'destroy'])->name('shippings.delete');

           //discount Coupons route
           Route::get('/discountCoupons', [DiscountCouponsController::class, 'index'])->name('discount-coupons.index');
           Route::get('/discountCoupons/create', [DiscountCouponsController::class, 'create'])->name('discount-coupons.create');
           Route::post('/discountCoupons', [DiscountCouponsController::class, 'store'])->name('discount-coupons.store');
           Route::get('/discountCoupons/{discountCoupon}/edite', [DiscountCouponsController::class, 'edite'])->name('discount-coupons.edite');
           Route::put('/discountCoupons/{discountCoupon}', [DiscountCouponsController::class, 'update'])->name('discount-coupons.update');
           Route::delete('/discountCoupons/{discountCoupon}', [DiscountCouponsController::class, 'destroy'])->name('discount-coupons.delete');

           //orders route
         Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
         Route::get('/orders/{id}', [OrderController::class, 'detial'])->name('orders.detial');
         Route::post('/order/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');


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


