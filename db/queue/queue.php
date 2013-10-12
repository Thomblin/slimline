<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 08.10.13
 * @time 19:51
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\queue;

use de\detert\sebastian\slimline\db\Handler;

class Queue
{
    /**
     * @var Handler
     */
    private $db;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $class;
    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $params;
    /**
     * @var string
     */
    private $unique = null;

    /**
     * @param Handler $db
     */
    public function __construct(Handler $db)
    {
        $this->db    = $db;
        $this->name  = 'default';
        $this->start = new \DateTime();
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start) {
        $this->start = $start;

        return $this;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $params
     */
    public function setCallback($class, $method, array $params) {
        $this->class  = $class;
        $this->method = $method;
        $this->params = $params;

        return $this;
    }

    /**
     * @param string $unique
     */
    public function setUnique($unique) {
        $this->unique = $unique;

        return $this;
    }

    public function createJob()
    {
        $job = new Job();

        $job->setName($this->name);
        $job->setStart($this->start->format('Y-m-d H:i:s'));
        $job->setCallback($this->class, $this->method, $this->params);
        $job->setUnique($this->unique);

        $this->db->insertModel($job);
    }

    public function updateJob()
    {
        $job = new Job();

        $job->setWorker(0);
        $job->setName($this->name);
        $job->setStart($this->start->format('Y-m-d H:i:s'));
        $job->setCallback($this->class, $this->method, $this->params);
        $job->setUnique($this->unique);

        $this->db->saveModel($job);
    }
}