<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use League\Plates\Engine;

class SubscriptionController
{
    public function subscriptionForm(ServerRequestInterface $request, ResponseInterface $response)
    {
        $serverParams = $request->getServerParams();

        echo "Incoming connection time: " . date('Y-m-d H:i:s') . "\n";
        echo "Incoming connection uri: " . $serverParams['REQUEST_URI'] ?? '' . "\n";

        $templates = new Engine(__DIR__ . '/../html');
        $html_content = $templates->render('sample5', [
            'main_heading' => 'Subscription Page',
        ]);

        $response->getBody()->write($html_content);
        return $response->withStatus(200);
    }

    public function subscribe(ServerRequestInterface $request, ResponseInterface $response)
    {
        $serverParams = $request->getServerParams();

        $data = $request->getParsedBody();

        echo "Incoming connection time: " . date('Y-m-d H:i:s') . "\n";
        echo "Incoming connection uri: " . $serverParams['REQUEST_URI'] ?? '' . "\n";
        echo "Incoming connection data: " . json_encode($data) . "\n";

        $templates = new Engine(__DIR__ . '/../html');
        $html_content = $templates->render('sample5-result', [
            'main_heading' => 'Subscription Page Result',
            'email' => $data['email'],
        ]);

        $response->getBody()->write($html_content);
        return $response->withStatus(200);
    }
}
