<?php

namespace FryskeOranjekoeke\Model;

/**
 * The Base Entity.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class Entity
{
    public $required = [
    ];

    public $types = [
    ];

    public function getRequired(): array
    {
        return $this->required ?? [];
    }

    public function patch(\stdClass $data): self
    {
        foreach ($data as $field => $value) {
            $this->{'set' . ucfirst($field)}($value);
        }
        return $this;
    }

    /**
     * Method which will be called when a method in this or one of its implementation classes which doesn't exist.
     *
     * @uses FryskeOranjekoeke\Model\Entity::doMagicGetSet() To perform a get/set for a variable when the called
     *                                                         function from @param $func contains `get` or `set`.
     *
     * @param string   $func   Containing the name of the function that was tried to be called.
     * @param array|[] $params Containing the parameters that were given when calling the function from @param $func.
     *
     * @return string When performing a magic `get` method.
     *         void   When performing a magic `set` method.
     */
    protected function __call(string $func, array $params = [])
    {
        // Perform magic get/set.
        if (in_array(substr($func, 0, 3), ['get', 'set'])) {
            return $this->doMagicGetSet($func, $params);
        }
    }

    /**
     * Magically sets or gets a class variable.
     *
     * @see FryskeOranjekoeke\Model\Entity::__call() for parameter and return type docs.
     */
    protected function doMagicGetSet(string $func, array $params = [])
    {
        switch (substr($func, 0, 3)) {
            case 'get':
                // Get the variable that was requested to get.
                $targetVariable = $this->getVariableNameFromGetSetFuncName($func);
                // Return void if no variable could be found else the variable value itself.
                return ($targetVariable === null) ?  : $this->{$targetVariable};
                break;

            case 'set':
                // Get the variable that was requested to be set.
                $targetVariable = $this->getVariableNameFromGetSetFuncName($func);
                // Only set the variable if its name is know.
                if ($targetVariable === null) {
                    return;
                }
                if (isset($params[0]) && (isset($this->types[$targetVariable])) && (gettype($params[0]) !== $this->types[$targetVariable])) {
                    throw new \TypeError();
                }
                $this->{$targetVariable} = ($params[0] ?? null);
                break;

            default:
                return;
        }
    }

    /**
     * Gets the name of the variable that is in a `get`/`set` function name.
     *
     * @param string $func Containing the name of the function to get the variable name from.
     *
     * @return null   When no variable name could be found.
     *         string Containing the found variable name.
     */
    private function getVariableNameFromGetSetFuncName(string $func): ?string
    {
        // Get the string after `get` or `set`.
        $targetVariable = substr($func, 3);
        if (empty($targetVariable)) {
            return null;
        }
        // When camelcase, the function called will look like this: `getVariable` and the substring from @var $targetVariable
        //   will then be `Variable`. But because class variables don't start with an capital letter, make the first
        //   character lowercase: `variable.`
        $targetVariable = lcfirst($targetVariable);
        return $targetVariable;
    }
}
