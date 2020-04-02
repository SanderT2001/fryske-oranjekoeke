<?php

namespace FryskeOranjekoeke\Partials;

class UrlBuilder extends Partial
{
    private $routes = [
    ];

    public function __construct()
    {
        parent::__construct();

        $this->routes = require (CONFIG . DS . 'routes.php');
    }

    public function searchForRoute(string $controller, string $action = 'index'): ?string
    {
        $output = null;
        foreach ($this->routes as $route => $controllerAction) {
            if (
                $controllerAction['controller'] === $controller &&
                $controllerAction['action']     === $action
            ) {
                $output = $route;
            }
        }
        return $output;
    }

    public function build(string $controller, string $action = 'index'): string
    {
        $this->validateControllerAction($controller, $action);
        return $this->constructUrl($controller, $action);
    }

    private function validateControllerAction(string $controller, string $action): void
    {
        if (!class_exists(get_app_class('controller', $controller, true))) {
            throw new \InvalidArgumentException('Controller does not exist');
        }
        // Get a new instance of the Controller in order to check if it has the action requested.
        $controller = get_app_class('controller', $controller);
        if (!method_exists($controller, $action)) {
            throw new \InvalidArgumentException('Action does not exist in ' . $controller . 'Controller');
        }
        return;
    }

    private function constructUrl(string $controller, string $action): string
    {
        $route = $this->searchForRoute($controller, $action);
        // Prepare route
        if ($route !== null) {
            $route = ltrim('/', $route);
        }

        $url = BASE_URL;
        $url .= ($route !== null) ? $route : ($controller . '/' . $action);
        return $url;
    }
}
