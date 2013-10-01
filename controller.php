<?php
namespace de\detert\sebastian\slimline;
use de\detert\sebastian\slimline\Exception as Exception;
use de\detert\sebastian\slimline\Exception\Handler as ExceptionHandler;

require_once 'pool.php';
require_once 'config.php';
require_once 'factory.php';

/**
 * this controller is used to handle the whole application execution
 * it contains an config interpreter, autoloader, renderer and error handling
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 22:05
 * @license property of Sebastian Detert
 */
class Controller
{
    /**
     * @var Pool
     */
    private $pool;
    /**
     * @var Response
     */
    private $response;

    /**
     * @param Config $config
     * @param Factory $factory
     */
    public function __construct(Config $config, Factory $factory)
    {
        $config->includes['de\detert\sebastian\slimline'] = realpath(__DIR__);

        $this->pool = $factory->create('de\detert\sebastian\slimline\Pool');
        $this->pool->config = $config;
        $this->pool->factory = $factory;

        spl_autoload_register(array($this, 'simpleAutoload'));

        date_default_timezone_set($config->timezone);

        $this->setHandlers();
        $this->setRequest();

        $this->pool->config->init($this->pool);
    }

    /**
     * @param string $className
     */
    private function simpleAutoload($className)
    {
        $className = strtolower($className);

        foreach ($this->pool->config->includes as $namespace => $docroot) {
            $path = $className;
            if ($namespace === substr($path, 0, strlen($namespace))) {
                $path = substr($path, strlen($namespace) + 1);
            }
            $path = $docroot . DS . str_replace(array('\\', '_'), DS, $path) . '.php';

            if (file_exists($path)) {
                include_once $path;
            }
        }
    }

    /**
     *
     */
    public function run()
    {
        try {
            $rules = $this->getRewriteRules();
            $this->setResponse($rules['callbacks']);
            $this->render($rules['render']);
        } catch (\Exception $e) {
            $this->response->exception = $e;
            $this->render($this->pool->config->renderError);
        }

    }

    /**
     *
     */
    private function setHandlers()
    {
        /** @var $exceptionHandler ExceptionHandler */
        $this->pool->factory->create($this->pool->config->exceptionHandler)
            ->setHandlers();
    }

    /**
     *
     */
    private function setRequest()
    {
        $this->pool->request = $this->pool->factory->create('de\detert\sebastian\slimline\Request');
    }

    /**
     * @param array $callbacks
     */
    private function setResponse(array $callbacks)
    {
        $this->response = $this->pool->factory->create('de\detert\sebastian\slimline\Response');

        foreach ($callbacks as $responseName => $callback) {
            $class = $callback[0];
            $action = $callback[1];

            $controller = $this->pool->factory->create($class);
            $this->response->$responseName = $controller->$action($this->pool);

            if ( ! $this->response->$responseName instanceof Response ) {
                throw new Exception\Error(
                    "Response of $class->$action() should be instance of de\detert\sebastian\slimline\Response: " .
                    get_class($this->response->$responseName) . " given"
                );
            }
        }
    }

    /**
     * @return array
     * @throws Exception\PageNotFound
     */
    private function getRewriteRules()
    {
        $rules = $this->pool->config->requestMap;

        $requestFiltered = $this->pool->request->getFilteredData(
            $this->pool->factory->create('de\detert\sebastian\slimline\Request_Filter_Controller')
        );

        if (empty($requestFiltered->redirect_url)) {
            $requestFiltered->redirect_url = '/';
        }

        if (!isset($rules[$requestFiltered->redirect_url])) {
            /** @var $exception Exception\PageNotFound */
            $exception = $this->pool->factory->create(
                'de\detert\sebastian\slimline\Exception\PageNotFound',
                $requestFiltered->redirect_url
            );
            throw $exception;
        }

        return $rules[$requestFiltered->redirect_url];
    }

    /**
     * @param array $callback
     */
    private function render(array $callback)
    {
        $class = $callback['call'][0];
        $action = $callback['call'][1];

        foreach ($callback['template'] as $filename) {
            $render = $this->pool->factory->create($class, $this->pool->config->templatePath);
            if ( isset($this->pool->translate) ) {
                $render->setTranslation($this->pool->translate);
            }
            $render->$action($filename, $this->response);
        }
    }
}
