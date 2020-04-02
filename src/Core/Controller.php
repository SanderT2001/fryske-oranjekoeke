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

    /**
     * Collection of Classes containing Logic that will be available to use in the Controllers.
     *   > Key:   The name of the Partial as how it will be callable in the Controllers.
     *   > Value: The Partial Class itself.
     *
     * @var array
     */
    protected $partials = [];

    public function __construct()
    {
        // Load all the Models for this Controller.
        $this->loadModels();

        // Load all the Partials for this Controller.
        $this->loadPartials();
    }

    public function getRequest(): RequestObject
    {
        return $this->request;
    }

    public function setRequest(RequestObject $request)
    {
        $this->request = $request;

        if ((bool) APP_CONFIG['runtime']['is_api'] === true) {
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

    public function getPartials(): array
    {
        return $this->partials;
    }

    public function getPartialPath(string $name)
    {
        return (FRYSKE_ORANJEKOEKE . DS . 'Partials' . DS . $name . '.php');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getPartial(string $name)
    {
        if (!isset($this->partials[$name])) {
            throw new \InvalidArgumentException('Partial not Loaded. Given Partial is: ' . $name);
        }
        return $this->partials[$name];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setPartial(string $name)
    {
        if (!is_file($this->getPartialPath($name))) {
            throw new \InvalidArgumentException('Partial File not found. Given Partial is: ' . $name);
        }

        $partial = strtr('FryskeOranjekoeke\Partials\$partial', [
            '$partial' => $name
        ]);
        $partialInstance = new $partial($this);
        $this->partials[$name] = $partialInstance;
        $this->{$name} = $partialInstance;
    }

    /**
     * Wrapper Function to be able to load all the Partials from @see Controller::models.
     *
     * @uses Controller::setPartial To be able to load a Single Partial.
     *
     * @return void
     */
    protected function loadPartials(): void
    {
        foreach ($this->getPartials() as $name) {
            $this->setPartial($name);
        }
    }
}
