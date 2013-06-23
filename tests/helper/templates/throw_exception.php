<?php
namespace de\detert\sebastian\slimline\Tests\Helper;

use de\detert\sebastian\slimline\Tests\Helper\Exception;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 11:53
 * @license property of Sebastian Detert
 */

/** @var $content mixed */
if ( is_string($content) ) {
    throw new Exception($content);
} elseif ( is_object($content) && isset($stdClassMessage) ) {
    throw new Exception($stdClassMessage);
} elseif ( is_array($content) && isset($arrayMessage) ) {
    throw new Exception($arrayMessage);
} else {
    throw new Exception('content is empty');
}