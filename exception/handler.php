<?php
namespace de\detert\sebastian\slimline\Exception;

/**
 * this Handler is used to throw Exceptions after assertion failures, errors, notices and warnings
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 20.01.13
 * @time 14:31
 * @license property of Sebastian Detert
 */
class Handler
{
    /**
     *
     */
    public function setHandlers()
    {
        $this->setAssertHandling();
        $this->setErrorHandling();
    }

    /**
     *
     */
    protected function setAssertHandling()
    {
        // Active assert and make it quiet
        assert_options (ASSERT_ACTIVE, 1);
        assert_options (ASSERT_WARNING, 0);
        assert_options (ASSERT_QUIET_EVAL, 1);

        // Set up the callback
        assert_options (ASSERT_CALLBACK, array($this, 'assertHandler'));
    }

    /**
     * @param string $file
     * @param int $line
     * @param int $code
     *
     * @throws Assert
     */
    public function assertHandler($file, $line, $code)
    {
        throw new Assert("'$code' @ $file in line $line");
    }

    /**
     *
     */
    protected function setErrorHandling()
    {
        set_error_handler(array($this, 'errorHandler'), E_ALL);
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     *
     * @return bool
     *
     * @throws Error
     */
    public function errorHandler($code, $message, $file, $line)
    {
        if ( 0 == error_reporting() ) {
            return true;
        }

        throw new Error("'$code: $message' @ $file in line $line");
    }
}
