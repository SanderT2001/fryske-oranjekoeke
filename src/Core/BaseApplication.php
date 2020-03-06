<?php

namespace FryskeOranjekoeke\Core;

require_once 'ConvenienceFunctions.php';
require_once 'MvcFunctions.php';
require_once FRYSKE_ORANJEKOEKE . DS . 'autoload.php';

/**
 * The Class that does it all.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class BaseApplication
{
    /**
     * @var RequestObject
     */
    protected $request = null;

    /**
     * @var Controller
     */
    protected $controller = null;

    public function getRequest(): RequestObject
    {
        return $this->request;
    }

    public function setRequest(RequestObject $request)
    {
        $this->request = $request;
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    protected function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Handles the whole request from Controller to View.
     *
     * @uses array $_SERVER To create the correct RequestObject which is then used to load the requested Controller and Action.
     */
    public function __construct()
    {
        // Make config globally available.
        define('APP_CONFIG', parse_ini_file(CONFIG . DS . 'config.ini', true));

        // Create the base Request Object.
        $this->setRequest(new RequestObject($_SERVER, require_once (CONFIG . DS . 'routes.php')));

        // Load the Requested Controller
        $this->loadController($this->getRequest()->getDestination()['controller']);
        // BeforeCall function available?
        if (method_exists($this->getController(), 'beforeCall')) {
            call_user_func_array([$this->getController(), 'beforeCall'], []);
        }
        // Call the Requested Action
        call_user_func_array([$this->getController(), $this->request->getDestination()['action']], $this->request->getDestination()['arguments'] ?? []);

        // Render the view
        $this->getController()->getView()->render();
    }

    /**
     * Loads a Controller by its name given @param $name;
     *
     * @param string $name Containing the name of the controller to load.
     *
     * @return Controller The loaded Controller.
     */
    private function loadController(string $name): Controller
    {
        $controller = get_app_class('controller', $name);
        $controller->setRequest($this->getRequest());
        $this->setController($controller);
        return $controller;
    }
}
