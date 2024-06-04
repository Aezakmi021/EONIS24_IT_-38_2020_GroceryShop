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
    Route::get('/admin-dashboard', [UserController::class, 'adminPage'])->middleware('admin.auth');

    // Admin categories manipulation
    Route::get('/categories', [CategoryController::class, 'viewPage'])->middleware('admin.auth');
    Route::post('/create-category', [CategoryController::class, 'store'])->middleware('admin.auth');
    Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->middleware('admin.auth');

// View listing for specific category
Route::get('/categories/{categoryId}', [CategoryController::class, 'showProducts'])->name('category.show');;

    //User manipulation routes
    Route::delete('/delete/{user}', [UserController::class, 'deleteUser'])->middleware('admin.auth');
    Route::get('/edit-user/{user}/edit', [UserController::class, 'viewUser'])->middleware('admin.auth');
    Route::put('/edit-user/{user}', [UserController::class, 'update'])->middleware('admin.auth');

    //Order routes for admin
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index')->middleware('admin.auth');
    Route::put('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->middleware('admin.auth');

    //Product manipulation
    Route::get('/create-product', [ProductController::class, 'showCreateForm'])->middleware('admin.auth');
    Route::post('/create-product', [ProductController::class, 'storeProduct'])->middleware('admin.auth');
    Route::delete('/product/{product}', [ProductController::class, 'delete'])->middleware('admin.auth');
    Route::get('/product/{product}/edit',[ProductController::class, 'showEditForm'])->middleware('admin.auth');
    Route::put('/product/{product}', [ProductController::class, 'update'])->middleware('admin.auth');

// Product routes for customers
Route::get('/search',[ProductController::class, 'search']);
Route::post('/product/{product}/comments', [ProductController::class, 'storeComment'])->middleware('auth');
Route::get('/product/{product}', [ProductController::class, 'viewSingleProduct']);

// User Routes
//Odvojiti login od homepage-a
Route::get('/', [UserController::class, 'showCorrectHomepage'])->name('login');
Route::get('/register', function () { return view('register'); })->middleware('guest');
Route::get('/profile', [UserController::class, 'profile'])->middleware('auth');
Route::get('/my-orders', [UserController::class, 'viewOrders'])->name('my-orders')->middleware('auth');

Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth');

Route::put('/update-profile', [UserController::class, 'updateProfile'])->middleware('auth');

Route::get('/notifications/{id}/mark-as-read', [NotificationController::class,'markAsRead'])->name('notifications.markAsRead')->middleware('admin.auth');




