<?php
/**
 * PHP File that defines all the required Paths.
 */

// Shorthand for the Directory Separator
define('DS', DIRECTORY_SEPARATOR);

// \app directory
define('ROOT', dirname(__DIR__, 1));
define('BASE_URL', str_replace('index.php', '', $_SERVER['PHP_SELF']));

// App
define('CONFIG',      ROOT . '/config');
define('TMP',         ROOT . '/tmp');
define('SRC',         ROOT . '/src');
define('ASSETS',      BASE_URL);
define('CONTROLLERS', SRC  . '/Controllers');
define('MODELS',      SRC  . '/Models');
define('ENTITIES',    SRC  . '/Models/Entities');
define('VIEWS',       SRC  . '/Views');
define('APP',         CONTROLLERS);

// Fryske Orangjekoeke
define('FRYSKE_ORANJEKOEKE', ROOT . '/vendor/sandert2001/fryske-oranjekoeke/src');
