<?php

class Router
{
    private $routes = [];

    public function add($route, $action)
    {
        $this->routes[$route] = $action;
    }

    public function getAction($route)
    {
        return $this->routes[$route] ?? null;
    }
}

// Voorbeeld van hoe je de router zou kunnen gebruiken
$router = new Router();

// ...bestaande routes

$router->add('voedselpakketten/afgerond', 'Voedselpakketten@afgerond');

// ...bestaande routes