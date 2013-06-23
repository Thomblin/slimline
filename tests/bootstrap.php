<?php
use de\detert\sebastian\slimline\Controller;
use de\detert\sebastian\slimline\Factory;
use de\detert\sebastian\slimline\Config;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('BASE_DIR') || define('BASE_DIR', realpath(__DIR__ . DS . '..') . DS );

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . DS . 'helper' . DS . 'test_case.php';

require_once BASE_DIR . 'controller.php';
$controller = new Controller(new Config(realpath(BASE_DIR)), new Factory());