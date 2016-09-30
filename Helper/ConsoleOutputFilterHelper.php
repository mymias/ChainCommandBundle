<?php
/*
 * This file is part of the NimiasChainCommandBundle package.
 *
 * (c) Mykolay Miasnikov <mykolmias@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimias\ChainCommandBundle\Helper;

/**
 * Filter for console output stream
 *
 * Essential for logging console command output
 *
 * @See \Nimias\ChainCommandBundle\Service\CommandEventsSubscriber::processCommand
 */
class ConsoleOutputFilterHelper extends \php_user_filter
{
    public function filter($in, $out, &$consumed, $closing)
    {
        try {

            while($bucket = stream_bucket_make_writeable($in)) {
                // Add output data to global holder
                OutputBufferHelper::add($bucket->data);
                \stream_bucket_append($out, $bucket);
            }

            return \PSFS_PASS_ON;

        } catch(\Exception $e) {
            @trigger_error($e->getMessage(), E_USER_ERROR);

            return \PSFS_ERR_FATAL;
        }
    }
}