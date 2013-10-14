<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 08.10.13
 * @time 19:39
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\queue;

use de\detert\sebastian\slimline\db\Model;
use de\detert\sebastian\slimline\Pool;

class Job extends Model
{
    /**
     * @var array
     */
    protected $columns = array(
        'name', // varchar(25) not null
        'start', // datetime not null
        'job', // text not null
        'worker', // smallint(5) unsigned not null
        'unique', // varchar(50)
    );

    /**
     * @var string
     */
    protected $tableName = 'queue';

    /**
     * @param varchar $name varchar(25) not null
     */
    public function setName($name) {
        $this->data['name'] = $name;
    }

    /**
     * @return boolean
     */
    public function issetName() {
        return isset($this->data['name']);
    }

    /**
     * @return varchar
     */
    public function getName() {
        return $this->data['name'];
    }

    /**
     * @param datetime $start datetime not null
     */
    public function setStart($start) {
        $this->data['start'] = $start;
    }

    /**
     * @return boolean
     */
    public function issetStart() {
        return isset($this->data['start']);
    }

    /**
     * @return datetime
     */
    public function getStart() {
        return $this->data['start'];
    }

    /**
     * @param text $job text not null
     */
    public function setJob($job) {
        $this->data['job'] = $job;
    }

    /**
     * @return boolean
     */
    public function issetJob() {
        return isset($this->data['job']);
    }

    /**
     * @return text
     */
    public function getJob() {
        return $this->data['job'];
    }

    /**
     * @param smallint $worker smallint(5) unsigned not null
     */
    public function setWorker($worker) {
        $this->data['worker'] = $worker;
    }

    /**
     * @return boolean
     */
    public function issetWorker() {
        return isset($this->data['worker']);
    }

    /**
     * @return smallint
     */
    public function getWorker() {
        return $this->data['worker'];
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $params
     */
    public function setCallback($class, $method, array $params) {
        $this->data['job'] = serialize(array($class, $method, $params));
    }

    /**
     * @param Pool $pool
     *
     * @return mixed
     */
    public function execute(Pool $pool) {
        list($class, $method, $params) = unserialize($this->data['job']);

        $object = new $class($pool);

        return call_user_func_array(array($object, $method), array($params));
    }

    /**
     * @param varchar $unique varchar(50)
     */
    public function setUnique($unique) {
        $this->data['unique'] = $unique;
    }

    /**
     * @return boolean
     */
    public function issetUnique() {
        return isset($this->data['unique']);
    }

    /**
     * @return varchar
     */
    public function getUnique() {
        return $this->data['unique'];
    }
}