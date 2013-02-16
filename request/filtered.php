<?php
namespace de\detert\sebastian\slimline;

/**
 * Request_Filtered is an result of Request::getFilteredData and contains all validated input variables
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 15:59
 * @license property of Sebastian Detert
 */
class Request_Filtered
{
    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
      array_walk_recursive($values, array($this, 'set'));
    }

    /**
     * set new key/value pair of a filtered and valid request to be public accessible
     *
     * @param string $value
     * @param string $name
     */
    private function set($value, $name)
    {
        $name = strtolower($name);
        $name = preg_replace('/[ ]/', '_', $name);
        $name = preg_replace('/[^a-z0-9_]/', '', $name);

        if ( !empty($name) ) {
            $this->$name = $value;
        }
    }
}
