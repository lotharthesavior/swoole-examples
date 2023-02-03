<?php

// *** server.php is the main entry point of the app ***

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../routes/router.php";

// openswoole classes
use OpenSwoole\Http\Server;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;

// initialize server
$server = new Server("127.0.0.1", 9501);

// server configuration
$server->set([
    // server settings
]);

// triggered when the server starts, connections are accepted after this callback is executed
$server->on("start", function ($server) {
    echo "OpenSwoole server is started at http://127.0.0.1:9501\n";
});

// the main HTTP server request callback event, entry point for all incoming HTTP requests
$server->on("request", function (Request $request, Response $response) use ($dispatcher) {
    // cors policy headers

    // Get the request URI and method
    $uri = $request->server["request_uri"];
    $method = $request->server["request_method"];

    // Dispatch the request
    $routeInfo = $dispatcher->dispatch($method, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // 404 Not Found
            $response->status(404);
            $data = array(
                "msg" => "404 Not Found"
            );
            $response->end(json_encode($data));
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            // 405 Method Not Allowed
            $response->status(405);
            $data = array(
                "msg" => "405 Method Not Allowed"
            );
            $response->end(json_encode($data));
            break;
        case FastRoute\Dispatcher::FOUND:
            $controllerName = $routeInfo[1];
            $vars = $routeInfo[2];

            // check case for either regular or middleware route
            switch ($controllerName) {

                    // if $controllerName is of type string then it must be a regular route
                case gettype($controllerName) === "string":
                    // call appropriate controller for the route
                    require_once __DIR__ . "/../controllers/" . $controllerName . ".php";
                    call_user_func($controllerName, $request, $response, $vars);
                    break;

                    // if $controllerName is of type array then it must be a middleware route
                case gettype($controllerName) === "array":
                    // call controller after executing the middleware function
                    $next = function () use ($controllerName, $request, $response, $vars) {
                        require_once __DIR__ . "/../controllers/" . $controllerName[1] . ".php";
                        call_user_func($controllerName[1], $request, $response, $vars);
                    };

                    // call the middleware function
                    require_once __DIR__ . "/../middlewares/" . $controllerName[0] . ".php";
                    call_user_func($controllerName[0], $request, $response, $vars, $next);
                    break;
            }
    }
});

// start server
$server->start();
