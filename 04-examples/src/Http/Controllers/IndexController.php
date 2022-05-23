<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use League\Plates\Engine;

class IndexController
{
    public function index (ServerRequestInterface $request, ResponseInterface $response)
    {
        $serverParams = $request->getServerParams();
        
        echo "Incoming connection time: " . date('Y-m-d H:i:s') . "\n";
        echo "Incoming connection uri: " . $serverParams['REQUEST_URI'] ?? '' . "\n";

        $query = $request->getQueryParams();

        $templates = new Engine(__DIR__ . '/../html');
        $html_content = $templates->render('sample4', [
            'main_heading' => 'My Page Title',
            'content' => 'The page\'s Body goes here... ' . ($query['content'] ?? ''),
        ]);

        $response->getBody()->write($html_content);
        return $response->withStatus(200);
    }
}
