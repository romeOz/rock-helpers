<?php

namespace rockunit\common;


use rock\helpers\File;

trait CommonTrait
{
    protected static function clearRuntime()
    {
        $runtime = ROCKUNIT_RUNTIME;
        File::deleteDirectory($runtime);
    }

    protected static function sort($value)
    {
        ksort($value);
        return $value;
    }
} 