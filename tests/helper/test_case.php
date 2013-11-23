<?php
namespace de\detert\sebastian\slimline\Tests\Helper;

use de\detert\sebastian\slimline\db\Config;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 20:05
 * @license property of Sebastian Detert
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $dbConfig;

    public function __construct()
    {
        parent::__construct();

        $this->dbConfig = new Config();

        // TODO read from general config file
        $this->dbConfig->dbName   = isset($_ENV["TRAVIS"])
            ? 'myapp_test'
            : 'slimline_test';

        $this->dbConfig->dsn      = isset($_ENV["TRAVIS"])
            ? 'mysql:dbname=myapp_test;host=127.0.0.1'
            : 'mysql:dbname=slimline_test;host=127.0.0.1';

        $this->dbConfig->user     = isset($_ENV["TRAVIS"])
            ? 'travis'
            : 'slimline';

        $this->dbConfig->password = '';
    }
}