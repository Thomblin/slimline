<?php
namespace de\detert\sebastian\slimline;

use de\detert\sebastian\slimline\Response;

/**
 * the Responce class is just an empty container which is used to deliver values from all controller to templates
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 13.12.12
 * @time 23:38
 * @license property of Sebastian Detert
 */
class Response_Debug_Sql extends Response
{
    private $debug = array();
    private $last  = 0;

    /**
     * @param string $sql
     * @param array  $params
     */
    public function start($sql, array $params = array())
    {
        $this->last = count($this->debug);
        $this->debug[$this->last] = array(
            'number'    => $this->last + 1,
            'sql'       => $sql,
            'params'    => $params,
            'start'     => microtime(true),
            'backtrace' => $this->getBacktrace(),
        );
    }

    public function getBacktrace()
    {
        $bt = debug_backtrace();
        array_pop($bt);

        foreach ( $bt as $k => $v ) {
            unset($bt[$k]['args'], $bt[$k]['object']);
        }

        return $bt;
    }

    public function stop()
    {
        $this->debug[$this->last]['stop'] = microtime(true);
        $this->debug[$this->last]['time'] = $this->debug[$this->last]['stop'] - $this->debug[$this->last]['start'];
    }

    public function getDebug()
    {
        $debug = $this->debug;

        uasort($debug, function($a, $b) {
            return $a['time'] < $b['time'];
        });

        return $debug;
    }

    public function profile(\PDO $db)
    {
        $db->query('set @@profiling = 1');
    }

    public function stopProfile(\PDO $db)
    {
        try {
            $this->debug[$this->last]['profile'] = $db->query('SHOW PROFILE')->fetchAll(\PDO::FETCH_ASSOC);
            $db->query('set @@profiling = 0');
        } catch(\Exception $e) {

        }
    }

    public function explain(\PDO $db)
    {
        try {
            $sth = $db->prepare('EXPLAIN ' . $this->debug[$this->last]['sql']);
            $sth->execute($this->debug[$this->last]['params']);

            $this->debug[$this->last]['explain'] = $sth->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\Exception $e) {

        }
    }
}
