<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultController('Auth');

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

$routes->get('/', function () {
    return view('landing');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

$routes->get(
    'dashboard',
    'Dashboard::index',
    ['filter' => 'auth']
);


/*
|--------------------------------------------------------------------------
| Pemasukan
|--------------------------------------------------------------------------
*/

$routes->get(
    'pemasukan',
    'Pemasukan::index',
    ['filter' => 'auth']
);

$routes->post(
    'pemasukan/store',
    'Pemasukan::store',
    ['filter' => 'auth']
);

$routes->post(
    'pemasukan/update/(:segment)',
    'Pemasukan::update/$1',
    ['filter' => 'auth']
);

$routes->post(
    'pemasukan/delete/(:segment)',
    'Pemasukan::delete/$1',
    ['filter' => 'auth']
);

$routes->get(
    'pemasukan/detail/(:segment)',
    'Pemasukan::detail/$1',
    ['filter' => 'auth']
);


/*
|--------------------------------------------------------------------------
| Pengeluaran
|--------------------------------------------------------------------------
*/

$routes->get(
    'pengeluaran',
    'Pengeluaran::index',
    ['filter' => 'auth']
);

$routes->post(
    'pengeluaran/store',
    'Pengeluaran::store',
    ['filter' => 'auth']
);

$routes->get(
    'pengeluaran/detail/(:num)',
    'Pengeluaran::detail/$1',
    ['filter' => 'auth']
);


$routes->post(
    'pengeluaran/update/(:num)',
    'Pengeluaran::update/$1',
    ['filter' => 'auth']
);

$routes->get(
    'pengeluaran/delete/(:num)',
    'Pengeluaran::delete/$1',
    ['filter' => 'auth']
);

$routes->post(
    'pengeluaran/delete/(:num)',
    'Pengeluaran::delete/$1',
    ['filter' => 'auth']
);


/*
|--------------------------------------------------------------------------
| Laporan
|--------------------------------------------------------------------------
*/

$routes->get(
    'laporan',
    'Laporan::index',
    ['filter' => 'auth']
);

$routes->post(
    'laporan/buat',
    'Laporan::buat',
    ['filter' => 'auth']
);

$routes->get(
    'laporan/download/(:any)',
    'Laporan::download/$1',
    ['filter' => 'auth']
);

$routes->get(
    'laporan/hapus/(:num)',
    'Laporan::hapus/$1',
    ['filter' => 'auth']
);