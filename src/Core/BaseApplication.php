<?php

namespace FryskeOranjekoeke\Core;

require_once 'ConvenienceFunctions.php';
require_once 'MvcFunctions.php';
require_once FRYSKE_ORANJEKOEKE . DS . 'autoload.php';
//require_once FRYSKE_ORANJEKOEKE . DS . 'exception_handler.php';

use FryskeOranjekoeke\Core\RequestObject;

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

    /**
     * Handles the whole request from Controller to View.
     *
     * @uses array $_SERVER To create the correct RequestObject which is then used to load the requested Controller and Action.
     */
    public function __construct()
    {
        // Make config globally available.
        if (defined('APP_CONFIG') === false)
            define('APP_CONFIG', require_once CONFIG . DS . 'config.php');

        // Create the base Request Object.
        $this->setRequest(new RequestObject($_SERVER, require_once (CONFIG . DS . 'routes.php')));

        // Load the Requested Controller
        $this->loadController($this->getRequest()->getDestination()['controller']);
        // BeforeCall function available?
        if (method_exists($this->getController(), 'beforeCall')) {
            $returnValue = call_user_func_array([$this->getController(), 'beforeCall'], []);
            if (!empty($returnValue)) {
                echo $returnValue;
                return;
            }
        }

        // Call the Requested Action
        $returnValue = call_user_func_array([$this->getController(), $this->request->getDestination()['action']], $this->request->getDestination()['arguments'] ?? []);

        if ((bool) APP_CONFIG['runtime']['is_api'] === false && $returnValue === null)
            // Render the view
            $this->getController()->getView()->render();
        else
            echo $returnValue;
    }

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
