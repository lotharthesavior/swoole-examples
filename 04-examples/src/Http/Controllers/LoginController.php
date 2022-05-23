<?php

namespace App\Http\Controllers;

use App\Services\SessionTable;
use League\Plates\Engine;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class LoginController
{
    private function getUsers(): array
    {
        $johnDoe = new stdClass;
        $johnDoe->id = 1;
        $johnDoe->name = 'John Doe';
        $johnDoe->email = 'john@doe.com';
        $johnDoe->password = password_hash('secret', PASSWORD_DEFAULT);
        return [$johnDoe];
    }

    public function home(RequestInterface $request, ResponseInterface $response)
    {
        $templates = new Engine(ROOT_DIR . '/html');
        $response->getBody()->write($templates->render('home'));
        return $response;
    }

    public function login(RequestInterface $request, ResponseInterface $response, $args)
    {
        $templates = new Engine(ROOT_DIR . '/html');
        $response->getBody()->write($templates->render('login', ['message' => '']));
        return $response;
    }

    public function loginHandler(RequestInterface $request, ResponseInterface $response, $args)
    {
        $data = $request->getParsedBody();

        // TODO: validation

        $user = current(array_filter($this->getUsers(), function($item) use ($data) {
            return $item->email === $data['email'];
        }));

        // TODO: verify if the user was found

        if (!password_verify($data['password'], $user->password)) {
            return $response
                ->withHeader('Location', '/login?error=Failed to authenticate!')
                ->withStatus(302);
        }

        $session_table = SessionTable::getInstance();
        $session_table->set($request->session['id'], [
            'id' => $request->session['id'],
            'user_id' => $user->id,
        ]);

        return $response
            ->withHeader('Location', '/admin')
            ->withStatus(302);
    }

    public function logoutHandler(RequestInterface $request, ResponseInterface $response, $args)
    {
        // TODO: validation

        $session_table = SessionTable::getInstance();
        $session_table->set($request->session['id'], [
            'id' => $request->session['id'],
        ]);

        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }

    public function admin(RequestInterface $request, ResponseInterface $response, $args)
    {
        $session_table = SessionTable::getInstance();
        $session_data = $session_table->get($request->session['id']);

        $user = current(array_filter($this->getUsers(), function($item) use ($session_data) {
            return $item->id === $session_data['user_id'];
        }));

        $templates = new Engine(ROOT_DIR . '/html');
        $response->getBody()->write($templates->render('admin', ['user_name' => $user->name]));
        return $response;
    }
}