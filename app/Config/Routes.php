<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function () {
    return view('welcome_message');
});

/*
|--------------------------------------------------------------------------
| API ROUTES (Group: api)
|--------------------------------------------------------------------------
| Semua route di bawah ini diprefix dengan /api
| Filter 'cors' jalan global lewat $methods di Filters.php (lihat Config/Filters.php)
*/
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('dashboard-summary', 'DashboardController::summary');

    // ===== AUTH (Public, tidak butuh token) =====
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::register');
    $routes->post('logout', 'AuthController::logout', ['filter' => 'authfilter']);
    $routes->get('me', 'AuthController::me', ['filter' => 'authfilter']);

    // ===== RESOURCE ROUTES =====
    // GET (index/show) dibiarkan terbuka untuk publik/terautentikasi biasa,
    // sedangkan POST/PUT/DELETE diproteksi oleh 'authfilter' (Bearer Token)
    // lewat pengaturan per-method di Config/Filters.php (lihat array 'authfilter').

    $routes->resource('kategori', [
        'controller' => 'KategoriController',
        'except'     => '', // semua method aktif: index, show, create(POST), update(PUT), delete
    ]);

    $routes->resource('supplier', [
        'controller' => 'SupplierController',
    ]);

    $routes->resource('barang', [
        'controller' => 'BarangController',
    ]);

    // Histori transaksi: read-only dari sisi resource standar (insert dilakukan otomatis
    // oleh sistem saat stok barang berubah), tapi tetap disediakan create/delete untuk admin.
    $routes->resource('histori', [
        'controller' => 'HistoriController',
    ]);

    // Endpoint tambahan non-CRUD standar
    $routes->get('dashboard/summary', 'DashboardController::summary');

    // Endpoint khusus untuk Home.js: GET /api/dashboard-summary
    // (URL pakai tanda hubung "-", BUKAN slash "/", sesuai axios.get(apiUrl + '/api/dashboard-summary') di frontend)
    $routes->get('dashboard-summary', 'DashboardController::summaryFlat');
});
