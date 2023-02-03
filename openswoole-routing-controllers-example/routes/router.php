<?php

// Include the FastRoute library
require_once __DIR__ . "/../vendor/autoload.php";

// Create a new FastRoute dispatcher
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    // Define routes
    $r->addRoute("GET", "/", "_function_");
    $r->addRoute("POST", "/api/regular-route", "regular_route_controller");
    $r->addRoute("POST", "/api/middleware-route", ["middleware_route_middleware", "middleware_route_controller"]);
});

return $dispatcher;
