<?php

namespace de\detert\sebastian\slimline\session;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 23.06.13
 * @time 13:46
 * @license property of Sebastian Detert
 */
interface Handler
{
    /**
     * @return void
     */
    public function startSession();

    /**
     * @return bool
     */
    public function close();
    /**
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy($sessionId);
    /**
     *  @return bool
     */
    public function gc($maxLifetime);
    /**
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName);
    /**
     * @param string $sessionId
     *
     * @return string
     */
    public function read($sessionId);
    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write($sessionId, $sessionData);
}