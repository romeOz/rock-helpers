<?php

namespace rockunit;


use rock\base\ObjectTrait;
use rock\helpers\Instance;
use rock\helpers\InstanceException;

class InstanceTest extends \PHPUnit_Framework_TestCase
{
    public function testEnsure()
    {
        $instance = Instance::ensure('ensure', Ensure::className());
        $this->assertInstanceOf(Ensure::className(), $instance);
    }

    public function testEnsureThrowException()
    {
        $this->setExpectedException(InstanceException::className());
        Instance::ensure('unknown', 'unknown');
    }
}

class Ensure {
    use ObjectTrait;
}
