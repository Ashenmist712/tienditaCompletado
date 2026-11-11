<?php

<<<<<<< HEAD
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SearchController;

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
=======
>>>>>>> origin/pasarelaPagos/paypal
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
<<<<<<< HEAD
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest; // Para validar el correo
use Illuminate\Http\Request;

=======
use App\Http\Controllers\CartController;
// Admin Controllers
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
>>>>>>> origin/pasarelaPagos/paypal

Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/producto/{id}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/categoria/{id}', [ShopController::class, 'category'])->name('shop.category');

Route::get('/nosotros', [PageController::class, 'about'])->name('pages.about');
Route::get('/contacto', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/buscar', [SearchController::class, 'index'])->name('search.index');

Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
Route::post('/carrito/eliminar', [CartController::class, 'remove'])->name('cart.remove');
<<<<<<< HEAD
=======
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/checkout/{provider}', [PaymentController::class, 'start'])
    ->where('provider', 'paypal')
    ->name('payments.start');
Route::post('/checkout/paypal/capture/{order}', [PaymentController::class, 'capturePaypal'])
    ->middleware('signed')
    ->name('payments.paypal.capture');
Route::get('/pago/confirmacion/{order}', [PaymentController::class, 'success'])
    ->name('payments.success');
>>>>>>> origin/pasarelaPagos/paypal

Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout')->middleware('auth');

// Rutas de Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
<<<<<<< HEAD
    Route::resource('categorias', CategoryController::class)->names('categories');
    Route::resource('productos', ProductController::class)->names('products');
    Route::resource('pedidos', OrderController::class)->names('orders');
    Route::resource('usuarios', UserController::class)->names('users');
=======

    Route::resource('categorias', CategoryController::class);
    Route::resource('productos', ProductController::class);
    Route::resource('pedidos', OrderController::class);
    Route::resource('usuarios', UserController::class);
>>>>>>> origin/pasarelaPagos/paypal
});
// Rutas de Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');





// --- RUTAS DE REGISTRO ---
Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

// --- RUTAS DE VERIFICACIÓN DE CORREO ---
// 1. Pantalla que dice "Revisa tu correo"
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. La ruta a la que el usuario es dirigido cuando hace clic en el enlace del correo
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // Marca el correo como verificado
    return redirect('/')->with('success', '¡Correo verificado exitosamente!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Ruta para reenviar el correo si no les llegó
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '¡Enlace de verificación reenviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');