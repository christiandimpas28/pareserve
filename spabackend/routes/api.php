<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BooksController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\ListingCategoryController;
use App\Http\Controllers\Api\ProductAttributesController;
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

//php artisan route:list --path=api -- List all routes under api 

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    // $res = Model::where('your query')->get();
    // $res->makeHidden(['column_one','column_two','column_n']);
    // return response()->json($res);
    return $request->user();
});

Route::get('/verified-email', function (){
    return response()->json([
       'message' => 'The email account is already confirmed.'  
    ]);
})->middleware('auth:sanctum', 'verified');


Route::post('/login', [AuthController::class, 'login'])
                ->middleware('guest')
                ->name('api.login');


// Route::get('categories', [CategoryController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    //Using apiResource
    // Route::apiResource('/categories', CategoryController::class);
    // Route::apiResource('/merchants', MerchantController::class)->only([
    //     'store', 'update', 'destroy', 'index'
    // ]);

    //Merchant
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::get('/partner/profile', [MerchantController::class, 'show']);
    Route::post('/partner/profile', [MerchantController::class, 'update']);
    Route::delete('/partner/profile/{merchant}', [MerchantController::class, 'destroy']);

    
    Route::get('/v2/partner/profile', [MerchantController::class, 'showV2']);

    Route::get('/partner/listings', [ListingCategoryController::class, 'index']);
    Route::get('/partner/listings/{listingCategory}', [ListingCategoryController::class, 'show']);
    Route::post('/partner/listings', [ListingCategoryController::class, 'store']);
    Route::put('/partner/listings/{listingCategory}', [ListingCategoryController::class, 'update']);
    Route::delete('/partner/listings/{listingCategory}', [ListingCategoryController::class, 'destroy']);
    Route::delete('/partner/listings/delete/photo/{listingCategory}', [ListingCategoryController::class, 'deletePhoto']);

    Route::get('/partner/products', [ProductController::class, 'index']);
    Route::post('/partner/product', [ProductController::class, 'store']);
    Route::put('/partner/product/{product}', [ProductController::class, 'update']);
    Route::get('/partner/product/{product}', [ProductController::class, 'show']);
    Route::delete('/partner/product/{product}', [ProductController::class, 'destroy']);
    Route::delete('/partner/product/delete/photo/{product}', [ProductController::class, 'deletePhoto']);
    
    Route::get('/partner/product/attr', [ProductAttributesController::class, 'index']);
    Route::post('/partner/product/attr', [ProductAttributesController::class, 'store']);
    Route::delete('/partner/product/attr/{productAttributes}', [ProductAttributesController::class, 'destroy']);
    Route::get('/partner/product/attr/{productAttributes}', [ProductAttributesController::class, 'show']);
    
    
    //Customer
    Route::get('/customer/profile', [CustomerProfileController::class, 'show']);
    Route::post('/customer/profile', [CustomerProfileController::class, 'update']);
    Route::get('/customer/bookings', [BooksController::class, 'customerTransactions']);

    //Booking
    Route::post('/booking/submit/{product}', [BooksController::class, 'store']);
    Route::get('/booking/confirm-and-pay/{books}/{slug}', [BooksController::class, 'show']);

    //Payment
    Route::post('/payment/submit/{books}', [PaymentController::class, 'pay']);
    Route::post('/payment/success/{bookedSummary}/{booking_ref_no}', [PaymentController::class, 'paymentSuccess']);
    Route::post('/payment/cancelled/{bookedSummary}/{booking_ref_no}', [PaymentController::class, 'paymentCancelled']);
    Route::post('/payment/fail/{bookedSummary}/{booking_ref_no}', [PaymentController::class, 'paymentFail']);

    Route::get('/partner/bookings', [BooksController::class, 'merchantTransactions']);
    Route::post('/partner/checkout/{books}', [BooksController::class, 'checkout']);

});

//Search
Route::get('/products', [SearchController::class, 'index']);
Route::get('/product/search', [SearchController::class, 'search']);
Route::get('/product/{product}/{slug}', [SearchController::class, 'viewProduct']);

//Booking
Route::get('/booking/pre-check/{product_id}', [BooksController::class, 'availability']);



// Listing Category

Route::group(['prefix'=> 'listings', 'middleware'=>'auth:sanctum'], function(){
    Route::get('/', [ListingCategoryController::class, 'index']);
});

//Keys
Route::get('/partner/developer', [DeveloperController::class, 'devkeys']);

