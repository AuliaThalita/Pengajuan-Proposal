<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// Semua pengguna yang sudah login
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('proposal', 'Proposal::index');
    $routes->get('proposal/(:num)', 'Proposal::show/$1');
    $routes->get('dokumen/(:num)', 'Dokumen::unduh/$1');
});

// UKM: mengajukan & memperbaiki proposal
$routes->get('proposal/baru', 'Proposal::create', ['filter' => 'auth:ukm']);
$routes->post('proposal', 'Proposal::store', ['filter' => 'auth:ukm']);
$routes->get('proposal/(:num)/ubah', 'Proposal::edit/$1', ['filter' => 'auth:ukm']);
$routes->post('proposal/(:num)/ubah', 'Proposal::update/$1', ['filter' => 'auth:ukm']);

// Pembina UKM: verifikasi
$routes->post('proposal/(:num)/review', 'Proposal::review/$1', ['filter' => 'auth:pembina']);

// Birmawa: validasi + data master
$routes->post('proposal/(:num)/validasi', 'Proposal::validasi/$1', ['filter' => 'auth:birmawa']);
$routes->group('admin', ['filter' => 'auth:birmawa'], static function ($routes) {
    $routes->get('ukm', 'Admin::ukm');
    $routes->post('ukm', 'Admin::ukmSimpan');
    $routes->post('ukm/(:num)/hapus', 'Admin::ukmHapus/$1');
    $routes->get('pengguna', 'Admin::pengguna');
    $routes->post('pengguna', 'Admin::penggunaSimpan');
    $routes->post('pengguna/(:num)/hapus', 'Admin::penggunaHapus/$1');
});
