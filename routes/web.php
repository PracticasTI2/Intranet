<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisicionesController;
use App\Http\Controllers\Eventoscontroller;

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
//     return view('welcome')->middleware('auth');
// });

$controller_path = 'App\Http\Controllers';

Route::get('/', $controller_path . '\dashboard\Analytics@index')->name('dashboard-analytics')->middleware('auth');
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');



//requis
Route::post('/requisiciones/enviar', $controller_path . '\RequisicionesController@pendiente')->name('requisiciones-pendiente');
Route::get('/requisiciones/create', $controller_path . '\RequisicionesController@create')->name('requisiciones-create')->middleware('auth');
Route::get('/requisiciones/index', $controller_path . '\RequisicionesController@index')->name('requisiciones-index')->middleware('auth');
Route::get('/requisiciones/historial', $controller_path . '\RequisicionesController@historial')->name('requisiciones-historial')->middleware('auth');
Route::get('/requisiciones/historialproductos', $controller_path . '\RequisicionesController@historialproductos')->name('requisiciones-historialproductos')->middleware('auth');
Route::get('/requisiciones/historico', $controller_path . '\RequisicionesController@historico')->name('requisiciones-historico')->middleware('auth');
Route::post('/requisiciones/store', $controller_path . '\RequisicionesController@store')->name('requisiciones-store')->middleware('auth');
Route::get('/requisiciones/show/{id}', $controller_path . '\RequisicionesController@show')->name('requisiciones-show')->middleware('auth');
Route::get('/requisiciones/{id}/edit', $controller_path . '\RequisicionesController@edit')->name('requisiciones-edit')->middleware('auth');
Route::put('/requisiciones/{id}',  $controller_path . '\RequisicionesController@update')->name('requisiciones-update')->middleware('auth');
Route::get('/ultimo-folio/{empresaId}', $controller_path . '\RequisicionesController@ultimoFolioPorEmpresa');
Route::delete('destroy/{id}', $controller_path . '\RequisicionesController@destroy')->name('requisiciones-destroy')->middleware('auth');
Route::post('/requisiciones/autorizar', $controller_path . '\RequisicionesController@autorizar')->name('requisiciones-autorizar');
Route::get('/requisicion/{id}/pdf', $controller_path . '\RequisicionesController@generatePdf')->name('requisicion.pdf')->middleware('auth');
Route::post('/requisiciones/entregado', $controller_path . '\RequisicionesController@entregado')->name('requisiciones-entregado');
Route::post('/exportexcel', $controller_path . '\RequisicionesController@exportexcel')->name('exportar.excel')->middleware('auth');

// Route::get('/requisiciones/cotizacion/download/{file}', '\RequisicionesController@downloadCotizacion')->name('download-cotizacion');
Route::get('/requisiciones/cotizaciones/{file}', [RequisicionesController::class, 'downloadCotizacion'])->name('download-cotizacion');
Route::get('/requisiciones/cotizaciones/view/{file}', [RequisicionesController::class, 'viewCotizacion'])->name('view-cotizacion');
Route::delete('/destroycoti/{id}', [RequisicionesController::class, 'destroycoti'])->name('cotizacion.destroycoti');
Route::get('/requisiciones/{id}/duplicar', [RequisicionesController::class, 'duplicar'])->name('requisiciones-duplicar')->middleware('auth');
Route::post('/requisiciones/update-precios', [RequisicionesController::class, 'updatePrecios'])->name('requisiciones.updatePrecios')->middleware('auth');

//usuarios
Route::get('/usuarios/index', $controller_path . '\UsuariosController@index')->name('usuarios-index')->middleware('auth');
Route::get('/usuarios/create', $controller_path . '\UsuariosController@create')->name('usuarios-create')->middleware('auth');
Route::post('/usuarios/store', $controller_path . '\UsuariosController@store')->name('usuarios-store')->middleware('auth');
Route::get('usuarios/show/{id}', $controller_path . '\UsuariosController@show')->name('usuarios-show')->middleware('auth');
Route::get('/usuarios/{id}/edit', $controller_path . '\UsuariosController@edit')->name('usuarios-edit')->middleware('auth');
Route::put('/usuarios/{id}', $controller_path . '\UsuariosController@update')->name('usuarios-update')->middleware('auth');
Route::delete('destroy/{id}', $controller_path . '\UsuariosController@destroy')->name('usuarios-destroy')->middleware('auth');


//areas
Route::get('/usuarios/areas/index', $controller_path . '\AreasController@index')->name('areas-index')->middleware('auth');
Route::get('/usuarios/areas/create', $controller_path . '\AreasController@create')->name('areas-create')->middleware('auth');
Route::post('/usuarios/areas/store', $controller_path . '\AreasController@store')->name('areas-store')->middleware('auth');
Route::get('/usuarios/areas/show/{id}', $controller_path . '\AreasController@show')->name('areas-show')->middleware('auth');
Route::get('/usuarios/areas/{id}/edit', $controller_path . '\AreasController@edit')->name('areas-edit')->middleware('auth');
Route::put('/areas/{id}', $controller_path . '\AreasController@update')->name('areas-update')->middleware('auth');
Route::delete('destroy/{id}', $controller_path . '\AreasController@destroy')->name('areas-destroy')->middleware('auth');


//tableros
Route::get('/tableros/create', $controller_path . '\TablerosController@create')->name('tableros-create')->middleware('auth');
Route::get('/tableros/index', $controller_path . '\TablerosController@index')->name('tableros-index')->middleware('auth');
Route::get('/tableros/historico', $controller_path . '\TablerosController@historico')->name('tableros-historico')->middleware('auth');
Route::post('/tableros/store', $controller_path . '\TablerosController@store')->name('tableros-store')->middleware('auth');
Route::get('/tableros/show/{id}', $controller_path . '\TablerosController@show')->name('tableros-show')->middleware('auth');
Route::get('/tableros/{id}/edit', $controller_path . '\TablerosController@edit')->name('tableros-edit')->middleware('auth');
Route::put('/tableros/{id}',  $controller_path . '\TablerosController@update')->name('tableros-update')->middleware('auth');
Route::delete('/tableros/{id}', $controller_path . '\TablerosController@destroy')->name('tableros-destroy');
Route::get('/pantalla', $controller_path . '\TablerosController@pantalla')->name('tableros.pantalla');


//roles
Route::get('/roles/index', $controller_path . '\RoleController@index')->name('roles-index')->middleware('auth');
Route::get('/roles/create', $controller_path . '\RoleController@create')->name('roles-create')->middleware('auth');
Route::post('/roles/store', $controller_path . '\RoleController@store')->name('roles-store')->middleware('auth');
Route::get('/roles/show/{id}', $controller_path . '\RoleController@show')->name('roles-show')->middleware('auth');
Route::get('/roles/{id}/edit', $controller_path . '\RoleController@edit')->name('roles-edit')->middleware('auth');
Route::put('/roles/{id}', $controller_path . '\RoleController@update')->name('roles-update')->middleware('auth');
Route::delete('roles/{id}', $controller_path . '\RoleController@destroy')->name('roles-destroy')->middleware('auth');


//permisos
Route::get('/permisos/index', $controller_path . '\PermissionController@index')->name('permisos-index')->middleware('auth');
Route::get('/permisos/create', $controller_path . '\PermissionController@create')->name('permisos-create')->middleware('auth');
Route::post('/permisos/store', $controller_path . '\PermissionController@store')->name('permisos-store')->middleware('auth');
Route::get('/permisos/show/{id}', $controller_path . '\PermissionController@show')->name('permisos-show')->middleware('auth');
Route::get('/permisos/{id}/edit', $controller_path . '\PermissionController@edit')->name('permisos-edit')->middleware('auth');
Route::put('/permisos/{id}', $controller_path . '\PermissionController@update')->name('permisos-update')->middleware('auth');
Route::delete('permisos/{id}', $controller_path . '\PermissionController@destroy')->name('permisos-destroy')->middleware('auth');


//cotizaciones
Route::get('/cotizaciones/create', $controller_path . '\CotizacionesController@create')->name('cotizaciones-create')->middleware('auth');
Route::get('/cotizaciones/index', $controller_path . '\CotizacionesController@index')->name('cotizaciones-index')->middleware('auth');
Route::get('/cotizaciones/historico', $controller_path . '\CotizacionesController@historico')->name('cotizaciones-historico')->middleware('auth');
Route::post('/cotizaciones/store', $controller_path . '\CotizacionesController@store')->name('cotizaciones-store')->middleware('auth');
Route::get('/cotizaciones/show/{id}', $controller_path . '\CotizacionesController@show')->name('cotizaciones-show')->middleware('auth');
Route::get('/cotizaciones/{id}/edit', $controller_path . '\CotizacionesController@edit')->name('cotizaciones-edit')->middleware('auth');
Route::put('/cotizaciones/{id}',  $controller_path . '\CotizacionesController@update')->name('cotizaciones-update')->middleware('auth');
Route::get('/cotizaciones/cotizar/{id}', $controller_path . '\CotizacionesController@cotizar')->name('cotizaciones-cotizar')->middleware('auth');
Route::get('/cotizaciones/completas/{id}', $controller_path . '\CotizacionesController@completas')->name('cotizaciones-completas')->middleware('auth');
Route::get('/cotizaciones/cotisporautorizar', $controller_path . '\CotizacionesController@cotisporautorizar')->name('cotizaciones-cotisporautorizar')->middleware('auth');
Route::get('/cotizaciones/cotisliberadas', $controller_path . '\CotizacionesController@cotisliberadas')->name('cotizaciones-cotisliberadas')->middleware('auth');
Route::get('/cotizaciones/cotizacionautoriza/{id}', $controller_path . '\CotizacionesController@cotizacionautoriza')->name('cotizaciones-cotizacionautoriza')->middleware('auth');
Route::post('/autorizar-cotizacion',  $controller_path . '\CotizacionesController@autorizarCotizacion')->name('autorizar-cotizacion')->middleware('auth');
Route::post('/guardar-factura/{requisicion_id}', $controller_path . '\CotizacionesController@guardarFactura')->name('guardar.factura')->middleware('auth');
Route::post('/rechazar-cotizaciones', $controller_path . '\CotizacionesController@rechazarCotizaciones')->name('rechazar.cotizaciones');
Route::post('/cotizaciones/entregado', $controller_path . '\CotizacionesController@entregado')->name('cotizaciones-entregado');


// Calendario - vacaciones
Route::get('/vacaciones/calendario', $controller_path . '\EventosController@indexCalendario')->name('vacaciones-calendario')->middleware('auth');
Route::get('/listar', $controller_path . '\EventosController@listar')->name('listar');
Route::post('/verificarDisponibilidad', $controller_path . '\EventosController@verificarDisponibilidad')->name('verificarDisponibilidad');
Route::post('/registrar', $controller_path . '\EventosController@registrar')->name('registrar');
Route::delete('/eliminarEvento/{id}', $controller_path . '\EventosController@eliminarEvento')->name('eliminarEvento');
Route::post('/autorizar/{id}', $controller_path . '\EventosController@autorizar')->name('autorizar');

// Agenda 
Route::get('/vacaciones/agenda', $controller_path . '\EventosController@indexAgenda')->name('vacaciones-agenda')->middleware('auth');
Route::get('/listarAgenda', $controller_path . '\EventosController@listarAgenda')->name('listarAgenda');

// Rutas de prueba

Route::get('/prueba-menu', function () {
    return view('prueba-menu');
})->middleware('auth')->name('prueba-menu');


