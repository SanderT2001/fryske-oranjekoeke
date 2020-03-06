<?php
/**
 * PHP File containing a collection of MVC Functions.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */

if (!function_exists('get_app_class')) {
    /**
     * Gets the Class in the `App` Namespace.
     *
     * @param string     $type           The type of file to load, ex. Controller or Model.
     * @param string     $name           The name of the file in the type to load.
     * @param bool|false $returnPathOnly Telling what to return, a new instance of the class to get or the full class Namespace Path.
     *
     * @return string When @param $returnPathOnly is true.
     *         class  When @param $returnPathOnly is false.
     */
    function get_app_class(string $type, string $name, bool $returnPathOnly = false)
    {
        $path = 'App\$type\$name';
        switch (strtolower($type)) {
            case 'controller':
                $type = 'Controller';
                $name = ($name . 'Controller');
                break;

            case 'model':
                $type = 'Models';
                break;

            case 'entity':
                $type = 'Models\Entities';
                break;
        }
        $path = strtr($path, [
            '$type' => $type,
            '$name' => $name
        ]);
        return ($returnPathOnly) ? $path : new $path();
    }
}
