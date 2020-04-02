<?php

namespace App\Controller;

class ErrorController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->getView()->setLayout('error');
    }

    public function beforeCall()
    {
        parent::beforeCall();
    }

    public function handleException(\Exception $e)
    {
        $this->getView()
             ->setView('Error', 'exception')
             ->setData([
                 'title' => 'Exception Thrown - Fryske Oranjekoeke',
                 'message' => $e->getMessage()
             ])
             ->render();
    }
}
