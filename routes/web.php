<?php

if (app()->environment('local', 'testing')) {
    require __DIR__ . '/debug.php';
}

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\categoriaController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\compraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\ExportPDFController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\InventarioControlller;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\logoutController;
use App\Http\Controllers\marcaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\presentacioneController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\proveedorController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\userController;
use App\Http\Controllers\ventaController;
use App\Http\Controllers\ProductoVarianteController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Auth;
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

use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/coleccion', [PublicController::class, 'collection'])->name('collection');
Route::get('/producto/{id}', [PublicController::class, 'show'])->name('product.show');
Route::get('/contacto', [PublicController::class, 'contact'])->name('contact');
Route::get('/nosotros', [PublicController::class, 'about'])->name('about');


Route::middleware('auth')->get('/panel', [homeController::class, 'index'])->name('panel');



Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::resource('presentaciones', presentacioneController::class)->except('show');
    Route::resource('marcas', marcaController::class)->except('show');
    Route::get('productos/export', [ProductoController::class, 'export'])->name('productos.export');
    Route::post('productos/import', [ProductoController::class, 'import'])->name('productos.import');
    Route::resource('productos', ProductoController::class)->except('show', 'destroy');
    Route::resource('clientes', clienteController::class)->except('show');
    Route::resource('proveedores', proveedorController::class)->except('show');
    Route::resource('compras', compraController::class)->except('edit', 'update', 'destroy');
    Route::resource('ventas', ventaController::class)->except('edit', 'update', 'destroy');
    Route::resource('users', userController::class)->except('show');
    Route::resource('roles', roleController::class)->except('show');
    Route::resource('profile', profileController::class)->only('index', 'update');
    Route::resource('activityLog', ActivityLogController::class)->only('index');
    Route::resource('inventario', InventarioControlller::class)->except('show');
    Route::resource('kardex', KardexController::class)->only('index');
    Route::resource('empresa', EmpresaController::class)->only('index', 'update');
    Route::resource('empleados', EmpleadoController::class)->except('show');
    Route::resource('cajas', CajaController::class)->except('edit', 'update', 'show');
    Route::resource('movimientos', MovimientoController::class)->except('show', 'edit', 'update', 'destroy');
    Route::resource('categorias', categoriaController::class)->except('show');

    // Variantes de producto (talla/color)
    Route::resource('productos.variantes', ProductoVarianteController::class)->except('show');

    // Importaciones
    Route::resource('importaciones', ImportacionController::class)->only('index', 'create', 'store', 'show');

    // Devoluciones y cambios
    Route::resource('devoluciones', DevolucionController::class)->only('index', 'create', 'store', 'show');
    Route::patch('devoluciones/{devolucion}/aprobar', [DevolucionController::class, 'aprobar'])->name('devoluciones.aprobar');
    Route::patch('devoluciones/{devolucion}/rechazar', [DevolucionController::class, 'rechazar'])->name('devoluciones.rechazar');

    // Reportes
    Route::get('/reportes/rentabilidad', [ReporteController::class, 'rentabilidad'])->name('reportes.rentabilidad');

    // Exportes PDF/Excel
    Route::get('/export-pdf-comprobante-venta/{id}', [ExportPDFController::class, 'exportPdfComprobanteVenta'])
        ->name('export.pdf-comprobante-venta');

    Route::get('/export-excel-vental-all', [ExportExcelController::class, 'exportExcelVentasAll'])
        ->name('export.excel-ventas-all');

    Route::post('/importar-excel-empleados', [ImportExcelController::class, 'importExcelEmpleados'])
        ->name('import.excel-empleados');

    Route::post('/notifications/mark-as-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');

    Route::get('/logout', [logoutController::class, 'logout'])->name('logout');
});



Route::get('/login', [loginController::class, 'index'])->name('login.index');
Route::post('/login', [loginController::class, 'login'])->name('login.login')->middleware('throttle:10,1');

// TEMPORARY: one-time migration/seed runner — remove after use
Route::get('/run-migrations-9x7k2p', function () {
    if (request('key') !== env('APP_KEY')) {
        abort(403);
    }
    try {
        $out = [];
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $out[] = \Illuminate\Support\Facades\Artisan::output();
        if (request('seed')) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => request('seed'), '--force' => true]);
            $out[] = \Illuminate\Support\Facades\Artisan::output();
        }
        return response()->json(['status' => 'ok', 'output' => implode("\n", $out)]);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});


