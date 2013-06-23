<?php
namespace de\detert\sebastian\slimline;

/**
 * class Request contains all superglobals, deletes them and return only filtered values
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 22:50
 * @license property of Sebastian Detert
 */
class Session extends Request
{
    /**
     *
     */
    protected function validateSuperglobals()
    {
        $this->data['SESSION'] = $this->normalize($_SESSION);
    }

    /**
     *
     */
    protected function unsetSuperglobals()
    {
        $_SESSION = array();
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @throws \Exception
     */
    public function set($name, $value)
    {
        if ( $name !== $this->normalize($name) ) {
            throw new \Exception("name '$name' is not allowed. try '{$this->normalize($name)}''");
        }

        $this->data['SESSION'][$name] = $value;
    }

    /**
     *
     */
    public function __destruct()
    {
        $_SESSION = $this->data['SESSION'];
    }
}
