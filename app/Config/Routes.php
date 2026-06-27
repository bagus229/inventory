<?php
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->options('(:any)', function() {});
$routes->post('api/login', 'Api\Auth::login'); 

$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard-summary', 'Api\Dashboard::summary');
    $routes->post('logout', 'Api\Auth::logout');
    $routes->get('kategori', 'Api\Kategori::index');
    $routes->post('kategori', 'Api\Kategori::create');
    $routes->put('kategori/(:num)', 'Api\Kategori::update/$1');
    $routes->delete('kategori/(:num)', 'Api\Kategori::delete/$1');
    $routes->get('barang', 'Api\Barang::index');
    $routes->post('barang', 'Api\Barang::create');
    $routes->put('barang/(:num)', 'Api\Barang::update/$1');
    $routes->delete('barang/(:num)', 'Api\Barang::delete/$1');
    $routes->get('supplier', 'Api\Supplier::index');
    $routes->post('supplier', 'Api\Supplier::create');
    $routes->put('supplier/(:num)', 'Api\Supplier::update/$1');
    $routes->delete('supplier/(:num)', 'Api\Supplier::delete/$1');
    $routes->get('histori', 'Api\HistoriBarang::index');
    $routes->post('histori', 'Api\HistoriBarang::create');
    $routes->put('histori/(:num)', 'Api\HistoriBarang::update/$1');
    $routes->delete('histori/(:num)', 'Api\HistoriBarang::delete/$1');
});
