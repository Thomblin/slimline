<?php
namespace de\detert\sebastian\slimline;

/**
 * Request_Filter is used to store data definitions according to filter_var()
 * Each allowed input variable should be stored as public variable
 *
 * e.g.
 *
 * public $name = array(
 *     'scope' => array('GET', 'POST', 'COOKIE', 'SERVER', 'CLI', 'USER'),
 *     'filter' => FILTER_DEFAULT,
 *     'options' => array('default' => 'sebastian'),
 * );
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 16:17
 * @license property of Sebastian Detert
 * @see http://www.php.net/manual/en/function.filter-var.php
 */
class Request_Filter_Controller extends Request_Filter
{
    /**
     * @var array
     * @see http://www.php.net/manual/en/function.filter-var.php
     */
    public $redirect_url = array(
      'scope' => array('SERVER'),
      'filter' => FILTER_SANITIZE_STRING,
      'options' => array('default' => '/'),
    );
}