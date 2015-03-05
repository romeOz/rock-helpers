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

        // as object
        $instance = Instance::ensure(new Ensure(), Ensure::className());
        $this->assertInstanceOf(Ensure::className(), $instance);

        // as Config
        $instance = Instance::ensure(['class' => Ensure::className()]);
        $this->assertInstanceOf(Ensure::className(), $instance);
    }

    public function testEnsureThrowException()
    {
        $this->setExpectedException(InstanceException::className());
        Instance::ensure('unknown', 'unknown');
    }

    public function testEnsureReturnNull()
    {
        $this->assertNull(Instance::ensure('unknown', 'unknown', false));
    }
}

class Ensure {
    use ObjectTrait;
}