<?php
/**
 * PHP File that defines all the required Paths.
 */

// Shorthand for the Directory Separator
define('DS', DIRECTORY_SEPARATOR);

// \app directory
define('ROOT', dirname(__DIR__, 1));

// App
define('CONFIG',      ROOT . '/config');
define('TMP',         ROOT . '/tmp');
define('SRC',         ROOT . '/src');
define('CONTROLLERS', SRC  . '/Controllers');
define('VIEWS',       SRC  . '/Views');

// Fryske Orangjekoeke
define('FRYSKE_ORANJEKOEKE', ROOT . '/vendor/sandert2001/fryske-oranjekoeke/src');
