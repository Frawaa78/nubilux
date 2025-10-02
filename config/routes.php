<?php

// Routes configuration
// This file will be loaded by the Router class

// Define custom routes here
// Example:
// $router->get('/dashboard', 'DashboardController@index');
// $router->post('/api/login', 'AuthController@login');

// API Routes
$router->get('/api/user', 'UserController@profile');
$router->post('/api/user/update', 'UserController@update');

// Dashboard routes
$router->get('/dashboard', 'DashboardController@index');

// Note: Legacy file-based routing is still supported
// index.php, login.php, register.php, etc. will continue to work