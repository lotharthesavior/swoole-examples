<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Exception;

class ExampleMiddleware
{
    /**
     * @param  Request  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        global $app;

        try {
            // @throws Exception
            $this->validateEmail($request);
        } catch (Exception $e) {
            $response = new Response();
            $routeParser = $app->getRouteCollector()->getRouteParser();
            $url = $routeParser->urlFor('subscriptionForm', [], ['error' => $e->getMessage()]);
            return $response
                ->withHeader('Location', $url)
                ->withStatus(302);
        }

        return $handler->handle($request);
    }

    /**
     * @param Request $request
     *
     * @return void
     *
     * @throws Exception
     */
    private function validateEmail(Request $request): void
    {
        $inputData = $request->getParsedBody();

        if (!isset($inputData['email']) || empty($inputData['email'])) {
            throw new Exception('Email is a required field.');
        }

        if (!filter_var($inputData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email.');
        }
    }
}
