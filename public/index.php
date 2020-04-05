<?php


require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


session_start();
/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('start', ['controller' => 'Start', 'action' => 'index']);
$router->add('revenue', ['controller' => 'Revenue', 'action' => 'index']);
$router->add('expense', ['controller' => 'Expense', 'action' => 'index']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
