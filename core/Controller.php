<?php

namespace Core;

use Core\Providers\PDOServiceProvider;
use Core\Providers\TwigServiceProvider;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\RedirectResponse;


class Controller
{
    private $config;
    private $view;
    private $urlGenerator;
    private $pdo;
    private $format;


    public function __construct()
    {
        $this->loadConfig();

        $routes = include __DIR__ . '/../src/routes.php';
        $this->urlGenerator = new UrlGenerator($routes, new Routing\RequestContext());

        $twig = new TwigServiceProvider($this->config['twig']);
        $pdo = new PDOServiceProvider($this->config['database']);
        $this->view = $twig->provide(array(
            'urlGenerator' => $this->urlGenerator,
            'getPathInfo' => Request::createFromGlobals()->getPathInfo()
        ));
        $this->pdo = $pdo->provide(array());
    }

    private function loadConfig()
    {
        $this->config = include(__DIR__ . '/../src/config.php');
    }

    /*
        public function render($name, $data = [], $statusCode = [])
        {
            if ($this->format == 'json') {
                $body = json_encode($data, JSON_UNESCAPED_UNICODE);
                
            } else {
                $body = $this->view->render($name, $data);
            }
    
            return new Response($body, $statusCode);
        }*/
    public function render($name, $data = [], $status = 200, $headers = [])
    {
        if ($this->format == 'json') {
            return new JsonResponse($data, $status, $headers);
        } else {
            $body = $this->view->render($name, $data);
            return new Response($body);
        }

    }

    public function pdo($class)
    {
        return new $class($this->pdo);
    }

    public function redirect($route)
    {
        return header('Location: ' . $route);
    }

    public function redirectToRoute($route, $parameters = array())
    {
        return new RedirectResponse($this->urlGenerator->generate($route, $parameters));
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function generateUrl($route, $parameters = array())
    {
        return $this->urlGenerator->generate($route, $parameters);
    }

    public function decodeToken()
    {
        $request = Request::createFromGlobals();
        $key = 'key_super_secure';
        $jwt = JWT::decode($request->headers->get('token'), $key, ['HS256']);
        
    }


}