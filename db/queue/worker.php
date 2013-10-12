<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 08.10.13
 * @time 19:51
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\queue;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\Pool;

class Worker
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Callback
     */
    private $callback;

    /**
     * @param Handler $db
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->repository->initQueueTable();
    }

    /**
     * @param Callback $callback
     */
    public function setCallback(Callback $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param Pool   $pool
     * @param string $name
     */
    public function run(Pool $pool, $name) {

        $pid = getmypid();

        do {
            try {
                $job = $this->repository->getNextJob($pid, $name);
            } catch(Exception_StillRunning $e) {
                $this->repository->finishJob($this->repository->getRunningJob($pid));
                continue;
            } catch(Exception_NoJobFound $e) {
                sleep(5);
                continue;
            } catch(\Exception $e) {
                if ( !is_null($this->callback) ) {
                    $this->callback->catchException($e);
                }
                continue;
            }

            try {
                $result = $job->execute($pool);
                $this->repository->finishJob($job);

                if ( !is_null($this->callback) ) {
                    $this->callback->catchJobResult($job, $result);
                }
            } catch(\Exception $e) {
                $this->repository->finishJob($job);
                if ( !is_null($this->callback) ) {
                    $this->callback->catchJobException($job, $e);
                }
            }
        } while(true);
    }
}