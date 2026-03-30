<?php

// debug.php removido por seguridad

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
use App\Http\Controllers\AgenteIAController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ventaController;
use App\Http\Controllers\ReservaController;
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
Route::get('/reservar', [ReservaController::class, 'index'])->name('reservar.index');
Route::post('/reservar', [ReservaController::class, 'store'])->name('reservar.store');


Route::middleware('auth')->get('/panel', [homeController::class, 'index'])->name('panel');
Route::middleware('auth')->get('/admin/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas.index');



Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    Route::resource('presentaciones', presentacioneController::class)->except('show');
    Route::resource('marcas', marcaController::class)->except('show');
    Route::get('productos/export', [ProductoController::class, 'export'])->name('productos.export');
    Route::post('productos/import', [ProductoController::class, 'import'])->name('productos.import');
    Route::get('productos/template', [ProductoController::class, 'downloadTemplate'])->name('productos.template');
    Route::post('productos/generate-description', [ProductoController::class, 'generateDescription'])->name('productos.generate-description');
    Route::post('productos/generate-all-descriptions', [ProductoController::class, 'generateAllDescriptions'])->name('productos.generate-all-descriptions');
    Route::post('productos/generate-from-images', [ProductoController::class, 'generateFromImages'])->name('productos.generate-from-images');
    Route::post('productos/crear-desde-imagenes', [ProductoController::class, 'crearDesdeImagenes'])->name('productos.crear-desde-imagenes');
    Route::post('productos/{producto}/remove-imagen', [ProductoController::class, 'removeImagen'])->name('productos.remove-imagen');
    Route::resource('productos', ProductoController::class)->except('show', 'destroy');
    Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::patch('productos/{producto}/toggle-estado', [ProductoController::class, 'toggleEstado'])->name('productos.toggle-estado');
    Route::resource('clientes', clienteController::class)->except('show');
    Route::resource('proveedores', proveedorController::class)->except('show');
    Route::post('compras/extract-factura', [compraController::class, 'extractFromFile'])->name('compras.extract-factura');
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


    //Reportes
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

    Route::post('/agente-ia/chat', [AgenteIAController::class, 'chat'])->name('agente.ia.chat');

    // Reservas
    Route::get('/reservas', [ReservaController::class, 'adminIndex'])->name('reservas.index');
    Route::patch('/reservas/{reserva}/estado', [ReservaController::class, 'updateEstado'])->name('reservas.estado');

    Route::get('/logout', [logoutController::class, 'logout'])->name('logout');

    // Emergencia: Ejecutar migraciones desde el navegador si no se tiene acceso por terminal
    Route::get('/migrate', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            return response()->json([
                'status' => 'success',
                'message' => 'Migraciones ejecutadas correctamente.',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al ejecutar migraciones: ' . $e->getMessage()
            ], 500);
        }
    });

    // Nueva ruta para renombrar al cliente John a General
    Route::get('/rename-client', function () {
        $persona = \App\Models\Persona::where('razon_social', 'John')->first();
        if ($persona) {
            $persona->update(['razon_social' => 'General']);
            return response()->json(['status' => 'success', 'message' => 'Cliente John renombrado a General con éxito.']);
        }
        return response()->json(['status' => 'error', 'message' => 'No se encontró ningún cliente llamado John.'], 404);
    });
});



Route::get('/login', [loginController::class, 'index'])->name('login.index');
Route::post('/login', [loginController::class, 'login'])->name('login.login');


