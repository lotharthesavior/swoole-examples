<?php 

function middleware_route_middleware($request, $response, $vars, $next) {
    echo("\nthis line was printed form the middleware function\n");
    $next();
}