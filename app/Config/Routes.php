<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', static function () {
    return view('welcome_message');
});

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {

    // =====================================================
    // CORS Preflight (OPTIONS)
    // =====================================================
    $routes->options('(:any)', static function () {});

    // =====================================================
    // PUBLIC ROUTES
    // =====================================================

    // Login
    $routes->post('login', 'AuthController::login');

    // Dashboard Summary
    $routes->get('dashboard-summary', 'DashboardController::summary');

    // =====================================================
    // PROTECTED ROUTES (JWT)
    // =====================================================
    $routes->group('', ['filter' => 'authfilter'], function ($routes) {

        // Auth
        $routes->post('logout', 'AuthController::logout');
        $routes->get('me', 'AuthController::me');

        // ===========================
        // KATEGORI
        // ===========================
        $routes->get('kategori', 'KategoriController::index');
        $routes->get('kategori/(:num)', 'KategoriController::show/$1');
        $routes->post('kategori', 'KategoriController::create');
        $routes->put('kategori/(:num)', 'KategoriController::update/$1');
        $routes->delete('kategori/(:num)', 'KategoriController::delete/$1');

        // ===========================
        // BARANG
        // ===========================
        $routes->get('barang', 'BarangController::index');
        $routes->get('barang/(:num)', 'BarangController::show/$1');
        $routes->post('barang', 'BarangController::create');
        $routes->put('barang/(:num)', 'BarangController::update/$1');
        $routes->delete('barang/(:num)', 'BarangController::delete/$1');

        // ===========================
        // SUPPLIER
        // ===========================
        $routes->get('supplier', 'SupplierController::index');
        $routes->get('supplier/(:num)', 'SupplierController::show/$1');
        $routes->post('supplier', 'SupplierController::create');
        $routes->put('supplier/(:num)', 'SupplierController::update/$1');
        $routes->delete('supplier/(:num)', 'SupplierController::delete/$1');

        // ===========================
        // HISTORI
        // ===========================
        $routes->get('histori', 'HistoriController::index');
        $routes->get('histori/(:num)', 'HistoriController::show/$1');
        $routes->post('histori', 'HistoriController::create');
        $routes->put('histori/(:num)', 'HistoriController::update/$1');
        $routes->delete('histori/(:num)', 'HistoriController::delete/$1');
    });

});