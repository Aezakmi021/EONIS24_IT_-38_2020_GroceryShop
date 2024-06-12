<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WishlistController;

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


// Wishlist
Route::get('/wishlist', [WishlistController::class, 'viewPage'])->middleware('auth');
Route::post('/wishlist/add/{product}', [WishlistController::class, 'add'])->middleware('auth');
Route::delete('/wishlist/remove/{product}', [WishlistController::class, 'remove'])->middleware('auth');

// Stripe
Route::post('/checkout', [StripeController::class, 'checkout']);
Route::get('/success', [StripeController::class, 'success'])->name('success');

// Cart
// Treba kasnije skloniti ovaj auth kada se uradi preko sesije pa ce i guest moci isto da trpa u karta
Route::get('/cart', [CartController::class, 'viewPage'])->name('cart')->middleware('auth');
Route::post('/cart/add/{product}', [CartController::class, 'addProduct'])->middleware('auth');
Route::delete('/cart/delete/{productId}', [CartController::class, 'deleteProduct'])->middleware('auth');
Route::put('/cart/update/{productId}', [CartController::class, 'updateQuantity'])->middleware('auth');

// Admin Routes
    // Admin-Dashboard
Route::prefix('admin-dashboard')->middleware('admin.auth')->group(function () {
    // Admin dashboard route
    Route::get('/', [UserController::class, 'adminPage'])->name('admin-dashboard');

    // Admin categories manipulation
    Route::post('/create-category', [CategoryController::class, 'store'])->name('create-category');
    Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->name('delete-category');
    Route::get('/edit-categories/{category}', [CategoryController::class, 'viewCategory'])->name('edit-category');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('update-category');

    // User manipulation routes
    Route::delete('/delete/{user}', [UserController::class, 'deleteUser'])->name('delete-user');
    Route::get('/edit-user/{user}', [UserController::class, 'viewUser'])->name('edit-user.edit');
    Route::put('/edit-user/{user}', [UserController::class, 'update'])->name('profile.update');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderController::class, 'updateStatus'])->name('orders.update.status');


    Route::get('/create-product', [ProductController::class, 'showCreateForm'])->name('create.product');
    Route::post('/create-product', [ProductController::class, 'storeProduct'])->name('create.product.save');
});


    Route::get('/categories', [CategoryController::class, 'viewPage'])->name('categories')->middleware('admin.auth');
    Route::get('/categories/{categoryId}', [CategoryController::class, 'showProducts'])->name('category.show');


    Route::delete('/product/{product}', [ProductController::class, 'delete']);
    Route::get('/product/{product}/edit',[ProductController::class, 'showEditForm']);
    Route::put('/product/{product}', [ProductController::class, 'update']);

// Product routes for customers
Route::get('/search',[ProductController::class, 'search']);
Route::post('/product/{product}/comments', [ProductController::class, 'storeComment'])->middleware('auth');
Route::get('/product/{product}', [ProductController::class, 'viewSingleProduct']);

// User Routes
Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::get('/register', function () { return view('register'); })->middleware('guest');

// profile prefix group
Route::prefix('profile')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'profile'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
    Route::get('/my-orders', [UserController::class, 'viewOrders'])->name('my-orders');

});

Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');


Route::get('/notifications/{id}/mark-as-read', [NotificationController::class,'markAsRead'])->name('notifications.markAsRead')->middleware('admin.auth');




