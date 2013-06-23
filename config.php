<?php
namespace de\detert\sebastian\slimline;

/**
 * class Config contains the most important specifications for your application, which will maybe change between
 * production, test and development versions
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 14.01.13
 * @time 20:00
 * @license property of Sebastian Detert
 */
class Config
{
    /**
     * map from url to controllers and templates
     * @var array
     */
    public $requestMap;
    /**
     * path to the root directory of your web application
     * @var string
     */
    public $baseDir;
    /**
     * @var array
     */
    public $includes = array();
    /**
     * path to the root directory of your templates
     * @var
     */
    public $templatePath;
    /**
     * name of an exception handler to define assert, error handling
     * @var string
     */
    public $exceptionHandler = 'de\detert\sebastian\slimline\Exception\Handler';
    /**
     * @var array
     */
    public $db = array();
    /**
     * display exception that were not catched
     * @var array
     */
    public $renderError = array(
        'template' => array(
            'exception.php',
        ),
        'call' => array('de\detert\sebastian\slimline\Render_Plain', 'render'),
    );

    /**
     * this method is called after autoloader was registered
     */
    public function init()
    {

    }
}
