<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->options('(:any)', function() {});

$routes->post('api/login', 'API\Auth::login');
$routes->get('api/dashboard-summary', 'API\Dashboard::summary');

$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->post('logout', 'API\Auth::logout');
    $routes->get('kategori', 'API\Kategori::index');
    $routes->post('kategori', 'API\Kategori::create');
    $routes->put('kategori/(:num)', 'API\Kategori::update/$1');
    $routes->delete('kategori/(:num)', 'API\Kategori::delete/$1');

    $routes->get('barang', 'API\Barang::index');
    $routes->post('barang', 'API\Barang::create');
    $routes->put('barang/(:num)', 'API\Barang::update/$1');
    $routes->delete('barang/(:num)', 'API\Barang::delete/$1');

    $routes->get('supplier', 'API\Supplier::index');
    $routes->post('supplier', 'API\Supplier::create');
    $routes->put('supplier/(:num)', 'API\Supplier::update/$1');
    $routes->delete('supplier/(:num)', 'API\Supplier::delete/$1');

    $routes->get('histori', 'API\HistoriBarang::index');
    $routes->post('histori', 'API\HistoriBarang::create');
    $routes->put('histori/(:num)', 'API\HistoriBarang::update/$1');
    $routes->delete('histori/(:num)', 'API\HistoriBarang::delete/$1');
});
