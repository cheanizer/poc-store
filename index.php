<?php

use Cheanizer\Poc\Controllers\Testing;
use Cheanizer\Poc\Controllers\Auth;
use Cheanizer\Poc\Controllers\Checkout;

require 'vendor/autoload.php';
require 'bootsrap.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    //testing route
    $r->addRoute('GET', '/test', Testing::class . '/test' );
    //get authentication
    $r->addRoute('POST','/auth/login',Auth::class . '/login');
    //checkout cart
    $r->addRoute('POST','/checkout',Checkout::class . '/checkout');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("/", $handler, 2);
        $class = new $class;
        $class->setConnection($conn);
        call_user_func_array(array($class, $method), $vars);
        break;
}