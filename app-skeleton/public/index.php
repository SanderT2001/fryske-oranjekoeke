<?php
/**
 * The Front Controller used for handling every incoming request.
 */

// @TODO Starttime aangeven microtime(true);
// @TODO realpath aangeven (__DIR__)

// Define the paths.
require '../config/paths.php';

// Startup the Application.
require '../src/Application.php';

// Enable error reporting
if (parse_ini_file('../config/config.ini', true)['runtime']['debug']) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Start the application.
use \App\Application;
$app = new Application();
