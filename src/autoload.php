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
    // Remove `FryskeOranjekoeke` from the path, because this is not the location of where to get the files from (where
    //   FryskeOranjekoeke is located). The correct path is set below.
    $className = str_replace($classNameSeperated[0], '', $className);

    // @TODO (Sander) Moet beter
    if ($classNameSeperated[0] == 'FryskeOranjekoeke') {
        require_once FRYSKE_ORANJEKOEKE . $className . '.php';
    } else {
        require_once APP . DS . $classNameSeperated[2] . '.php';
    }
});
