<?php
namespace de\detert\sebastian\slimline;

/**
 * Factory is a central class to create new objects
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 11:04
 * @license property of Sebastian Detert
 */
class Factory
{
    /**
     * @param string $className
     * @param mixed  $firstParameter
     * @param mixed  $_
     *
     * @return object
     */
    public function create($className, $firstParameter = null, $_ = null)
    {
        $args = func_get_args();
        array_shift($args);

        $reflection = new \ReflectionClass($className);

        if ( empty($args) ) {
            return $reflection->newInstance();
        } else {
            return $reflection->newInstanceArgs($args);
        }
    }
}
