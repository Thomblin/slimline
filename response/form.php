<?php

namespace de\detert\sebastian\slimline;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 29.09.13
 * @time 23:32
 * @license property of Sebastian Detert
 */

class Response_Form {
    const UNKOWN  = 0;
    const SUCCESS = 1;
    const FAILURE = 2;

    public $result      = self::UNKOWN;
    public $errorFields = array();
}