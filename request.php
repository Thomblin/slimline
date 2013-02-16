<?php
namespace de\detert\sebastian\slimline;

use de\detert\sebastian\slimline\Request_Filter;
use de\detert\sebastian\slimline\Request_Filtered;

/**
 * class Request contains all superglobals, deletes them and return only filtered values
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 22:50
 * @license property of Sebastian Detert
 */
class Request
{
    /**
     * @var array
     */
    private $data;

    /**
     *
     */
    public function __construct()
    {
        $this->validateSuperglobals();
        $this->unsetSuperglobals();
    }

    /**
     *
     */
    private function validateSuperglobals()
    {
        $this->data['SERVER'] = $this->normalize($_SERVER);
        $this->data['POST'] = $this->normalize($_POST);
        $this->data['GET'] = $this->normalize($_GET);
        $this->data['COOKIE'] = $this->normalize($_COOKIE);
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function normalize($value)
    {
        return is_array($value)
            ? array_map(__METHOD__, array_change_key_case($value, CASE_LOWER))
            : $value;
    }

    /**
     *
     */
    private function unsetSuperglobals()
    {
        $_SERVER = array();
        $_POST = array();
        $_GET = array();
        $_REQUEST = array();
        $_COOKIE = array();
    }

    /**
     * @param Request_Filter $filter
     *
     * @return Request_Filtered
     */
    public function getFilteredData(Request_Filter $filter)
    {
        $rules = get_object_vars($filter);
        $filtered_vars = array();

        foreach ( $rules as $name => $rule ) {
            foreach( $rule['scope'] as $scope ) {
                if ( isset($this->data[$scope][$name]) && false !== ($value = filter_var($this->data[$scope][$name], $rule['filter'], $rule['options'])) ) {
                    $filtered_vars[$name] = $value;
                    break;
                }
            }
        }

        return new Request_Filtered($filtered_vars);
    }
}
