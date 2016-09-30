<?php

namespace Nimias\ChainCommandBundle\Helper;


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