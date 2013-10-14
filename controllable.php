<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 12.10.13
 * @time 13:22
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline;

use de\detert\sebastian\slimline\db\Handler;

abstract class Controllable
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
     * @param Pool  $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @return Pool
     */
    protected function getPool()
    {
        return $this->pool;
    }

    /**
     * @return Response
     */
    protected function getResponse($responseClass = 'de\detert\sebastian\slimline\Response')
    {
        if ( empty($this->response) ) {
            $this->response = $this->pool->factory->create($responseClass);
        }

        return $this->response;
    }

    /**
     * @return Response
     */
    protected function getFilteredData($filterClass)
    {
        $filter  = $this->pool->factory->create($filterClass);
        $request = $this->pool->request->getFilteredData($filter);

        return $request;
    }
}