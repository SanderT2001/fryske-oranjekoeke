<?php

namespace FryskeOranjekoeke\View;

/**
 * The Object containing all the information and functions for a View.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class View
{
    /**
     * The name of the Layout File to load.
     *
     * @var string
     */
    protected $layout = null;

    /**
     * The name of the Parent Folder where the @var View::view is located.
     *
     * @var string
     */
    protected $parent = null;

    /**
     * The name of the View File to load.
     *
     * @var string
     */
    protected $view = null;

    /**
     * Collection of data to pass to the Views.
     *   > Key:   The name that the variable will have in the views.
     *   > Value: The data itself.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Collection of Classes containing Logic that will be available to use in the Views.
     *   > Key:   The name of the Partial as how it will be callable in the Views.
     *   > Value: The Partial Class itself.
     *
     * @var array
     */
    protected $partials = [
        'Content',
        'UrlBuilder',
        'HtmlTags'
    ];

    public function __construct()
    {
        // Load the required partials for this object to work..
        $this->loadPartials();
    }

    public function getLayoutPath(string $filename = null): string
    {
        if ($filename === null) {
            $filename = $this->layout;
        }

        return (VIEWS . DS . 'Layouts' . DS . $filename . '.php');
    }

    /**
     * @throws InvalidArgumentException When no File/View could be found which matches the parameter.
     */
    public function setLayout(string $name): self
    {
        if (!is_file($this->getLayoutPath($name))) {
            throw new \InvalidArgumentException('Layout File not found. Given path is: ' . $name);
        }

        $this->layout = $name;
        return $this;
    }

    public function getViewPath(string $parent = null, string $filename = null): ?string
    {
        if ($parent === null) {
            $parent = $this->parent;
        }
        if ($filename === null) {
            $filename = $this->view;
        }
        if ($parent === null && $filename === null) {
            return null;
        }

        return (VIEWS . DS . $parent . DS . $filename . '.php');
    }

    /**
     * Sets the View and Parent variables.
     *
     * @param string $parent   The name of the parent folder to set.
     * @param string $name     The name of the view in the parent folder.
     * @param bool   $validate Validate if the View File exists?
     *
     * @throws InvalidArgumentException When no File/View could be found which matches the parameters.
     */
    public function setView(string $parent, string $name = null, bool $validate = true): self
    {
        // No View.
        if (!$parent && $name === null) {
            $this->parent = $this->view = null;
            return $this;
        }

        if (!is_file($this->getViewPath($parent, $name)) && $validate) {
            throw new \InvalidArgumentException('View File not found. Given path is: ' . $parent . DS . $name);
        }

        $this->parent = $parent;
        $this->view   = $name;
        return $this;
    }

    public function getPartials(): array
    {
        return $this->partials;
    }

    public function getPartialPath(string $name)
    {
        return (FRYSKE_ORANJEKOEKE . DS . 'Partial' . DS . $name . '.php');
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
        if (explode('.', $name)[0] === 'App') {
            $name = explode('.', $name)[1];
            $this->partials[$name] = get_app_class('partial', $name);
        } else {
            if (!is_file($this->getPartialPath($name))) {
                throw new \InvalidArgumentException('Partial File not found. Given Partial is: ' . $name);
            }

            $partial = strtr('FryskeOranjekoeke\Partial\$partial', [
                '$partial' => $name
            ]);
            $this->partials[$name] = new $partial($this);
        }
    }

    /**
     * Sets a new data entry that will be passed on to the view.
     *
     * @param mixed      $name The name that this variable will have in the view.
     * @param mixed|null $data The data to set.
     */
    public function setData($name, $data = null): self
    {
        $data = $name;
        if (is_string($name) && $data === null) {
            $data = [$name => $data];
        }
        foreach ($data as $n => $d) {
            $this->data[$n] = $d;
        }
        return $this;
    }

    /**
     * Renders a Layout and the View.
     */
    public function render()
    {
        $layout = $this->getRequireContents($this->getLayoutPath());
        $view   = ($this->getViewPath() !== null) ? $this->getRequireContents($this->getViewPath()) : '';

        $content = $this->partials['Content']->parseContentBlocks($layout, $view);
        echo $content;
    }

    public function include(string $name)
    {
        $filepath = (VIEWS . DS . 'Includes' . DS . $name . '.php');
        return $this->getRequireContents($filepath);
    }

    /**
     * Gets the Contents of a Required File.
     * When getting the Contents, the Required File will have all the variables available from @var View::data, this is because we are talking about Views
     *   here which COULD need this data.
     *
     * @param string $filepath Containing the path of the File to Require the Contents for.
     *
     * @throws InvalidArgumentException When no File/View could be found by the given location in @param $filepath.
     *
     * @return string Containing the File Contents.
     */
    public function getRequireContents(string $filepath): string
    {
        if (!is_file($filepath)) {
            throw new \InvalidArgumentException('File not found. Given path is: ' . $filepath);
        }

        // Start with a clean output buffer.
        ob_start();
        // Make the data accessible to the output buffer/view, by their key.
        extract($this->data);
        // Make the Partials accessible to the output buffer/view.
        foreach ($this->partials as $name => $partial) {
            $this->{$name} = $partial;
        }
        // Require the view.
        require $filepath;
        // Get the output buffer and remove it.
        return ob_get_clean();
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
