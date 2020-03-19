<?php

/**
 * Class Autoloader for FryskeOranjekoeke.
 *
 * This function is automatically called by PHP when a Class is called, but not included/required.
 * This function will then auto include/require this Class (autoload).
 *
 * @param string $className Containing the name of the Class to load.
 *
 * @return void
 */
spl_autoload_register(function(string $className): void
{
    // Replace the Class Seperator with the directory seperator, ex. `FryskeOranjekoeke\View\View` => `FryskeOranjekoeke/View/View`.
    $className = str_replace('\\', DS, $className);
    $classNameSeperated = explode(DS, $className);

    $namespace = $classNameSeperated[0];
    $directories = $classNameSeperated;
    unset($directories[0]);

    // Load class
    $classPath = '';
    switch ($namespace) {
        case 'FryskeOranjekoeke':
            $classPath .= FRYSKE_ORANJEKOEKE;
            foreach ($directories as $directory) {
                $classPath .= (DS . $directory);
            }
            break;

        case 'App':
            // Determine which location to use.
            foreach ($directories as $directory) {
                switch ($directory) {
                    case 'Controller':
                        $classPath .= CONTROLLERS;
                        break;

                    case 'Models':
                        $classPath .= MODELS;
                        break;

                    default:
                        $classPath .= (DS . $directory);
                }
            }
            break;

        default:
            return;
    }
    $classPath = ($classPath . '.php');

    if (is_file($classPath) === false) {
        // @NOTE Don't throw an exception, because this will prevent PHP from trying other autoloaders to load the file when this autoloader cannot.
        // throw new \InvalidArgumentException('File not found, given path is ' . $classPath);
        return;
    }
    @require_once $classPath;
});
