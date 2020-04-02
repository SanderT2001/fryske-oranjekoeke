<?php

set_exception_handler(function(\Exception $e)
{
    $errorController = new App\Controller\ErrorController;
    $errorController->handleException($e);
});
