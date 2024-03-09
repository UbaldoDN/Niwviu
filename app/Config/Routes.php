<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->view('/', '/books/books');
$routes->view('users', '/users/users');
$routes->view('categories', '/categories/categories');
$routes->view('books', '/books/books');
$routes->view('borrowedBook', '/users/borrowedBook');

$routes->group('/api/v1', static function ($routes) {
    $routes->group('users', static function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('(:num)', 'Users::show/$1');
        $routes->post('', 'Users::store');
        $routes->put('(:num)', 'Users::update/$1');
        $routes->delete('(:num)', 'Users::delete/$1');
        $routes->post('(:num)/borrowedBook', 'Users::borrowedBook/$1'); 
        $routes->put('(:num)/getBackBorrowedBook', 'Users::getBackBorrowedBook/$1');
    });
    
    $routes->group('categories', static function ($routes) {
        $routes->get('/', 'Categories::index');
        $routes->post('', 'Categories::store');
        $routes->put('(:num)', 'Categories::update/$1');
        $routes->delete('(:num)', 'Categories::delete/$1');
    });
    
    $routes->group('books', static function ($routes) {
        $routes->get('/', 'Books::index');
        $routes->get('(:num)', 'Books::show/$1');
        $routes->post('', 'Books::store');
        $routes->put('(:num)', 'Books::update/$1');
        $routes->delete('(:num)', 'Books::delete/$1');
    });
});
