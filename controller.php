<?php
namespace de\detert\sebastian\slimline;
use de\detert\sebastian\slimline\Exception as Exception;
use de\detert\sebastian\slimline\Exception\Handler as ExceptionHandler;

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
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @param Config $config
     * @param Factory $factory
     */
    public function __construct(Config $config, Factory $factory)
    {
        $this->config = $config;
        $this->factory = $factory;

        $this->config->includes['de\detert\sebastian\slimline'] = realpath(__DIR__);

        spl_autoload_register(array($this, 'simpleAutoload'));
    }

    /**
     * @param string $className
     */
    private function simpleAutoload($className)
    {
        $className = strtolower($className);

        foreach ($this->config->includes as $namespace => $docroot) {
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
            $this->setHandlers();
            $this->setRequest();

            $rules = $this->getRewriteRules();
            $this->setResponse($rules['callbacks']);
            $this->render($rules['render']);
        } catch (\Exception $e) {
            print_r($e->getMessage());

            $this->response->exception = $e;
            $this->render($this->config->renderError);
        }

    }

    /**
     *
     */
    private function setHandlers()
    {
        /** @var $exceptionHandler ExceptionHandler */
        $exceptionHandler = $this->factory->create($this->config->exceptionHandler);

        if ($this->config->setAssertHandler) {
            $exceptionHandler->addAssertHandler();
        }

        if ($this->config->setErrorHandler) {
            $exceptionHandler->addErrorHandler();
        }
    }

    /**
     *
     */
    private function setRequest()
    {
        $this->request = $this->factory->create('de\detert\sebastian\slimline\Request');
    }

    /**
     * @param array $callbacks
     */
    private function setResponse(array $callbacks)
    {
        $this->response = $this->factory->create('de\detert\sebastian\slimline\Response');

        foreach ($callbacks as $responseName => $callback) {
            $this->response->$responseName = $this->factory->create('de\detert\sebastian\slimline\Response');

            $class = $callback[0];
            $action = $callback[1];

            $controller = $this->factory->create($class);
            $controller->$action($this->request, $this->response->$responseName);
        }
    }

    /**
     * @return array
     * @throws Exception\PageNotFound
     */
    private function getRewriteRules()
    {
        $rules = $this->config->requestMap;

        $requestFiltered = $this->request->getFilteredData(
            $this->factory->create('de\detert\sebastian\slimline\Request_Filter_Controller')
        );

        if (empty($requestFiltered->redirect_url)) {
            $requestFiltered->redirect_url = '/';
        }

        if (!isset($rules[$requestFiltered->redirect_url])) {
            /** @var $exception Exception\PageNotFound */
            $exception = $this->factory->create(
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
            $render = $this->factory->create($class, $this->config->templatePath);
            $render->$action($filename, $this->response);
        }
    }
}
