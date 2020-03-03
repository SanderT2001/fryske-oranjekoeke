<?php
/**
 * PHP File containing a collection of Convenient Functions.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */

if (!function_exists('pr')) {
    /**
     * Shorthand for PHP's build in `print_r`, but with pretty output. This means that the output will be printed with
     *   `<pre></pre>` tags surrounding the output.
     *
     * @param mixed $var The variable to print. Make sure this value is `print_r` compatible.
     */
    function pr($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

}

if (!function_exists('prd')) {
    /**
     * Shorthand for PHP's build in `print_r`, but with pretty output. This means that the output will be printed with
     *   `<pre></pre>` tags surrounding the output. This is then follewed by a `die();`.
     *
     * @param mixed $var The variable to print. Make sure this value is `print_r` compatible.
     */
    function prd($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        die();
    }
}

if (!function_exists('array_keys_exists')) {
    /**
     * Checks if multiple keys are present in an array.
     *
     * @link https://stackoverflow.com/questions/13169588/how-to-check-if-multiple-array-keys-exists
     *
     * @param array $keys Containing the keys to check for precense.
     * @param array $array Containing the array that must contain those keys.
     *
     * @return bool false When not all the keys are present.
     *              true  When all the keys are present.
     */
    function array_keys_exists(array $keys, array $arr): bool
    {
        return !array_diff_key(array_flip($keys), $arr);
    }
}

if (!function_exists('read_folder')) {
    /**
     * Gets all the Files a Folder.
     *
     * @param string $path              Containing the Path to get the files from.
     * @param bool   $returnFirst|false When true, this function only returns the first found file.
     * @param array  $forbiddenFiles|[] Containing the filenames that should be skipped from the output.
     *
     * @return array Containing the Files in the Folder, unless the parameter $returnFirst is true. Then only
     *                 the first File in the Folder is returned.
     */
    function read_folder(string $path, bool $returnFirst = false, array $forbiddenFilenames = []): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $output = [];
        // Merge the default forbidden files with the custom given @param $forbiddenFilenames.
        $forbiddenFilenames = array_merge(['.', '..'], $forbiddenFilenames);

        $files = scandir($path);
        foreach ($files as $filename) {
            if (in_array($filename, $forbiddenFilenames)) {
                continue;
            }

            $output[] = [
                'name' => $filename,
                'path' => ($path . $filename)
            ];

            if ($returnFirst) {
                break;
            }
        }

        return ($returnFirst ? $output[0] : $output);
    }
}

if (!function_exists('get_string_between')) {
    /**
     * Gets the string between the given start and endpoint.
     *
     * @link https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
     *
     * @param string $string The string to search in.
     * @param string $start  The startpoint of the string to get.
     * @param string $end    The endpoint of the string to get.
     *
     * @return string Containing the string between the given start and endpoint.
     */
    function get_string_between(string $string, string $start, string $end): string
    {
        // Lose the leading tab by adding a space
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
