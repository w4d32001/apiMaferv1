<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\DetailedProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Models\CustomerType;
use Illuminate\Support\Facades\Route;

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
// Rutas para autenticación de usuarios
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function($router){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

// Rutas para autenticación de clientes
Route::group([
    'middleware' => 'api',
    'prefix' => 'client'
], function($router){
    Route::post('login', [ClientController::class, 'login']);
        Route::post('logout', [ClientController::class, 'logout']);
        Route::post('refresh', [ClientController::class, 'refresh']);
        Route::post('me', [ClientController::class, 'me']);
});

// Ruta de prueba de correo electrónico
Route::get('send-test-mail', [MailTestController::class, 'sendTestMail']);

// Rutas protegidas con autenticación de API
Route::middleware('auth')->group(function() {
    Route::apiResource('category', CategoryController::class);
    Route::post('category/amount', [CategoryController::class, 'amount']);
    Route::post('detailedProduct/amount', [DetailedProductController::class, 'amount']);
    Route::post('product/amount', [ProductController::class, 'amount']);
    Route::post('provider/amount', [ProviderController::class, 'amount']);
    Route::apiResource('provider', ProviderController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('detailedProduct', DetailedProductController::class);
    Route::get('consultar-ruc/{rucNumber}', [ProviderController::class, 'consultarRuc']);
    Route::apiResource('rol', RolController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('userRole', UserRoleController::class);
    Route::put('userRole/role/{rol}', [UserRoleController::class, 'role']);
});

    Route::apiResource('inventory', InventoryController::class);

    Route::apiResource('company', CompanyController::class);
    
    Route::post('inventory/amount', [InventoryController::class, 'amount']);
    Route::post('customer/amount', [CustomerController::class, 'amount']);
    Route::apiResource('customerType', CustomerTypeController::class);
    Route::apiResource('customer', CustomerController::class);
    Route::get('consultar/{rucNumber}', [CustomerController::class, 'consultarRuc']);
    Route::match(['put', 'patch'], 'customer/address/{customer}', [CustomerController::class, 'updateAddress']);
    Route::apiResource('sale', SaleController::class);
    Route::get('payment', [PaymentMethodController::class, 'index']);
    Route::get('/sale/customer/{customerId}', [SaleController::class, 'orders']);
    Route::apiResource('order', OrderController::class);
    Route::get('order/showCustomer/{customer}', [OrderController::class, 'showCustomer']);
    Route::match(['put', 'patch'], 'order/orderPay/{id}', [OrderController::class, 'orderPay']);
    Route::match(['put', 'patch'], 'inventory/updateStock/{id}/stock/{stock}', [InventoryController::class, 'updateStock']);
    Route::match(['put', 'patch'], 'inventory/updateStatus/{id}', [InventoryController::class, 'updateStatus']);

    Route::post('/checkout', [PaymentController::class, 'apiCheckout']);
    Route::post('/validate', [PaymentController::class, 'apiValidate']);
    Route::post('/paid', [PaymentController::class, 'paid']);
    Route::post('/ipn', [PaymentController::class, 'ipn']);