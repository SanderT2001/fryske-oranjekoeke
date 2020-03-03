<?php

namespace FryskeOranjekoeke\View\Partials;

use \FryskeOranjekoeke\View\View as View;

class Partial
{
    /**
     */
    protected $view = null;

    public function __construct(View $view)
    {
        $this->view = $view;
    }
}
