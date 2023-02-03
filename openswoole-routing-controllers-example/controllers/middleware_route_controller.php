<?php

function middleware_route_controller($request, $response, $vars)
{
    var_dump($request->getContent());

    // response data
    $data = array(
        "msg" => "middleware_route_controller"
    );

    // send response in json
    $response->end(json_encode($data));
}
