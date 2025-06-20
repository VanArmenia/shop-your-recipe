<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guestOrVerified'])->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('home');
    Route::get('/product/category/{category:name}', [ProductController::class, 'category'])->name('product.category');

    Route::get('/product/{product:slug}', [ProductController::class, 'view'])->name('product.view');
    Route::post('/product/{product:id}/reviews', [ProductController::class, 'storeReview'])
        ->middleware('auth')->name('add-review');  // Ensure only authenticated users can post reviews
    Route::get('/product/{product:id}/reviews', [ProductController::class, 'fetchReviews'])->name('fetch-reviews');

    Route::get('/recipes/search', [RecipeController::class, 'search'])->name('recipes.search');
    Route::resource('recipes', RecipeController::class);
    Route::post('/recipes/{recipe:id}/reviews', [RecipeController::class, 'storeReview'])
        ->middleware('auth')->name('add-recipe-review');  // Ensure only authenticated users can post reviews
    Route::get('/recipes/{recipe:id}/reviews', [RecipeController::class, 'fetchReviews'])->name('fetch-recipe-reviews');
    Route::get('/recipes/category/{category:name}', [RecipeController::class, 'category'])->name('recipe.category');
    Route::get('/recipes/region/{region:name}', [RecipeController::class, 'region'])->name('recipe.region');
    Route::get('/recipes/ingredient/{ingredient:normalized_name}', [RecipeController::class, 'showRecipesByIngredient'])->name('recipe.ingredient');

    Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
    Route::view('/about', 'about')->name('about');

    Route::prefix('/cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{product:slug}', [CartController::class, 'add'])->name('add');
        Route::post('/bulk-add', [CartController::class, 'bulkAdd'])->name('bulk-add');
        Route::post('/remove/{product:slug}', [CartController::class, 'remove'])->name('remove');
        Route::post('/update-quantity/{product:slug}', [CartController::class, 'updateQuantity'])->name('update-quantity');
    });
});

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.update');
    Route::get('/profile/recipes', [ProfileController::class, 'recipes'])->name('profile.recipes');
    Route::post('/profile/recipes', [RecipeController::class, 'store'])->name('profile_recipes.create');
    Route::post('/profile/password-update', [ProfileController::class, 'passwordUpdate'])->name('profile_password.update');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder'])->name('cart.checkout-order');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failure', [CheckoutController::class, 'failure'])->name('checkout.failure');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/{order}', [OrderController::class, 'view'])->name('order.view');
});

Route::post('/webhook/stripe', [CheckoutController::class, 'webhook']);



require __DIR__ . '/auth.php';
