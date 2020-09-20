<?php
/**
 * The Front Controller used for handling every incoming request.
 */

// Define the paths.
require realpath('../config/paths.php');

// Startup the Application.
require realpath('../src/Application.php');

// Start the application.
use \App\Application;
$app = new Application();
