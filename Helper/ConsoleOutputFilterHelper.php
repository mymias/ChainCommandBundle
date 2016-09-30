<?php

namespace Nimias\ChainCommandBundle\Helper;

class ConsoleOutputFilterHelper extends \php_user_filter
{
    public function filter($in, $out, &$consumed, $closing)
    {
        try {

            while($bucket = stream_bucket_make_writeable($in)) {
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