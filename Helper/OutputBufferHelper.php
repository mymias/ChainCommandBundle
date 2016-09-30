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
 * Global holder of a stream output data.
 *
 * Essential for logging console command output
 *
 * @See \Nimias\ChainCommandBundle\Helper\ConsoleOutputFilterHelper::filter
 * @See \Nimias\ChainCommandBundle\Service\CommandEventsSubscriber::processTerminate
 */
class OutputBufferHelper
{
    private static $buffer = '';

    public static function add(string $data)
    {
        self::$buffer .= $data;
    }

    public static function fetch()
    {
        $existingBuffer = self::$buffer;
        self::$buffer = '';

        return $existingBuffer;
    }
}