<?php

set_exception_handler(function($e)
{
    $errorController = new App\Controller\ErrorController;
    $errorController->handleException($e);
});
