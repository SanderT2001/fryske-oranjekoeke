<?php

namespace FryskeOranjekoeke\Partial;

class UrlBuilder extends Partial
{
    private $routes = [
    ];

    public function __construct()
    {
        parent::__construct();

        $this->routes = require (CONFIG . DS . 'routes.php');
    }

    public function build(string $controller, string $action = 'index'): string
    {
        $this->validateControllerAction($controller, $action);
        return $this->constructUrl($controller, $action);
    }

    public function buildAssetUrl(string $type, string $name, bool $typeAsSuffix = true): string
    {
        switch (strtolower($type)) {
            case 'img':
                $extension = (pathinfo($name)['extension'] ?? null);
                // If no extension given, then fallback to the default extension: .png
                if ($extension === null) {
                    $name .= ('.png');
                }
                break;

            default:
                break;
        }

        $suffix = ($typeAsSuffix) ? ('.' . $type) : '';
        $location = (strpos($name, 'vendor') !== false) ? ($name . $suffix) : ($type . DS . $name . $suffix);
        // Remove duplicate /
        if ($location[0] === '/' && substr(ASSETS, -1) === '/') {
            $location = ltrim($location, '/');
        }
        return (ASSETS . $location);
    }

    private function validateControllerAction(string $controller, string $action): void
    {
        if (!class_exists(get_app_class('controller', $controller, true))) {
            throw new \InvalidArgumentException('Controller "'.$controller.'" does not exist');
        }
        // Get a new instance of the Controller in order to check if it has the action requested.
        $controller_class = get_app_class('controller', $controller);
        if (!method_exists($controller_class, $action)) {
            throw new \InvalidArgumentException('Action "'.$action.'" does not exist in ' . $controller . 'Controller');
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

    private function searchForRoute(string $controller, string $action = 'index'): ?string
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
}
