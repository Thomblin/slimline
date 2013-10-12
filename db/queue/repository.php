<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 08.10.13
 * @time 19:24
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\queue;

use de\detert\sebastian\slimline\db\Exception_NotFound;
use de\detert\sebastian\slimline\db\Handler;

class Repository
{
    /**
     * @var Handler
     */
    protected $db;

    /**
     * @param Handler $db
     */
    public function __construct(Handler $db)
    {
        $this->db = $db;
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->db;
    }

    public function initQueueTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `queue` (
            `name` VARCHAR(25) NOT NULL,
            `start` DATETIME NOT NULL,
            `job` TEXT NOT NULL,
            `worker` SMALLINT UNSIGNED NOT NULL,
            `unique` VARCHAR(50),
            INDEX `start` (`start`, `name`, `worker`),
            INDEX `worker` (`worker`),
            UNIQUE `unique` (`unique`)
         ) ENGINE=InnoDb DEFAULT charset=utf8";

        $this->db->query($sql);
    }

    /**
     * @param string $worker
     * @param string $queue
     *
     * @return Job
     *
     * @throws \de\detert\sebastian\slimline\db\Exception_NotFound
     * @throws Exception_StillRunning
     */
    public function getNextJob($worker, $queue)
    {
        $job = $this->getRunningJob($worker);

        if ( !empty($job) ) {
            throw new Exception_StillRunning();
        }

        $sql = "
            UPDATE
                `queue`
            SET
                `worker` = :worker
            WHERE
                `start` <= NOW() AND
                `name` = :name AND
                `worker` = 0
            ORDER BY `start` ASC LIMIT 1";
        $this->db->query($sql, array('worker' => $worker, 'name' => $queue));

        if ( $this->db->getAffectedRows() ) {
            $job = $this->getRunningJob($worker);
        }

        if ( empty($job) ) {
            throw new Exception_NoJobFound();
        }

        return $job;
    }

    /**
     * @param $worker
     *
     * @return Job
     */
    public function getRunningJob($worker)
    {
        try {
            $job = $this->db->loadModel('de\detert\sebastian\slimline\db\queue\Job', array('worker' => $worker));
        } catch(Exception_NotFound $e) {
            $job = null;
        }

        return $job;
    }

    /**
     * @param Job $job
     */
    public function finishJob(Job $job)
    {
        $sql = "DELETE FROM `queue` WHERE `worker` = :worker";
        $this->db->query($sql, array('worker' => $job->getWorker()));
    }
}