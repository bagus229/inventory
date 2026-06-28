<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function () {
    return view('welcome_message');
});
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    
    // ====================================================================
    // PERBAIKAN UTAMA: Izinkan METHOD OPTIONS untuk SEMUA endpoint di dalam /api
    // Ini menangani Preflight Request dari browser (termasuk Resource Routes)
    // ====================================================================
    $routes->options('(:any)', static function () {
        // Biarkan kosong, filter CORS global di Filters.php yang akan merespons dengan status 200
    });

    // ===== ENDPOINT SUMMARY =====
    $routes->get('dashboard-summary', 'DashboardController::summary');
    $routes->post('logout', 'Api\Auth::logout');
    // ===== AUTH (Public, tidak butuh token) =====
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::register');
    $routes->post('logout', 'AuthController::logout', ['filter' => 'authfilter']);
    $routes->get('me', 'AuthController::me', ['filter' => 'authfilter']);

    // ===== RESOURCE ROUTES =====
    $routes->resource('kategori', [
        'controller' => 'KategoriController',
    ]);

    $routes->resource('supplier', [
        'controller' => 'SupplierController',
    ]);

    $routes->resource('barang', [
        'controller' => 'BarangController',
    ]);

    $routes->resource('histori', [
        'controller' => 'HistoriController',
    ]);

    $routes->get('dashboard/summary', 'DashboardController::summary');
});