<?php
namespace de\detert\sebastian\slimline;

/**
 * this Pool is an alternative to classes in GLOBAL namespace or usage of singleton
 * this Pool just holds all main slimline objects to be delivered to each controller
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 25.05.13
 * @time 10:27
 * @license property of Sebastian Detert
 */
class Pool {
    /**
     * @var Request
     */
    public $request;
    /**
     * @var Config
     */
    public $config;
    /**
     * @var Factory
     */
    public $factory;
    /**
     * @var Db\Handler
     */
    public $dbHandler;
}