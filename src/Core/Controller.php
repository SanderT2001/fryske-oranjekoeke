<?php

namespace FryskeOranjekoeke\Core;

use FryskeOranjekoeke\{
    View\View as View
};

/**
 * The Controller.
 *
 * The Controller connects the Model and the View together.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class Controller
{
    /**
     * @var RequestObject
     */
    protected $request = null;

    /**
     * @var View
     */
    protected $view = null;

    /**
     * Array containing the names of all the Models to load and which will be available in the Controller to use by its given name.
     *
     * @var Array
     */
    protected $models = [];

    public function getRequest(): RequestObject
    {
        return $this->request;
    }

    public function setRequest(RequestObject $request)
    {
        $this->request = $request;

        if (APP_CONFIG['runtime']['is_api'] === true) {
            return;
        }

        $view = new View();
        $view->setLayout('default');
        $view->setView($request->getDestination()['controller'], $request->getDestination()['action'], false);
        $this->setView($view);
    }

    public function getView(): View
    {
        return $this->view;
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function getModels(): array
    {
        return $this->models;
    }

    public function __construct()
    {
        // Load all the Models for this Controller.
        $this->loadModels();
    }

    /**
     * Load a Single Model so it is available in the Controller by its name (@param $name).
     *
     * @param string $name The name of the Model to load.
     *
     * @return void
     */
    public function loadModel(string $name): void
    {
        $this->{$name} = get_app_class('model', $name);
    }

    /**
     * Wrapper Function to be able to load all the Models from @see Controller::models.
     *
     * @uses Controller::loadModel To be able to load a Single Model.
     *
     * @return void
     */
    protected function loadModels(): void
    {
        foreach ($this->getModels() as $name) {
            $this->loadModel($name);
        }
    }
}
