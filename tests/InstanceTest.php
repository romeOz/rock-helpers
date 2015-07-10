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

        $instance = Instance::ensure(Ensure::className());
        $this->assertInstanceOf(Ensure::className(), $instance);

        // as object
        $instance = Instance::ensure(new Ensure(), Ensure::className());
        $this->assertInstanceOf(Ensure::className(), $instance);

        // as Config
        $instance = Instance::ensure(['class' => Ensure::className()]);
        $this->assertInstanceOf(Ensure::className(), $instance);

        $instance = Instance::ensure(['class' => InstanceArgs::className(), 'foo' => 'foo test'], null, ['bar test']);
        $this->assertSame('foo test', $instance->foo);
        $this->assertSame('bar test', $instance->bar);

        $instance = Instance::ensure(['class' => InstanceArgs::className(), 'foo' => 'foo test'], InstanceArgs::className(), ['bar test']);
        $this->assertSame('foo test', $instance->foo);
        $this->assertSame('bar test', $instance->bar);
    }

    public function testEnsureThrowException()
    {
        $this->setExpectedException(InstanceException::className());
        Instance::ensure('unknown', 'unknown');
    }

    public function testEnsureReturnNull()
    {
        $this->assertNull(Instance::ensure('unknown', 'unknown', [], false));
    }
}

class Ensure {
    use ObjectTrait;
}

class InstanceArgs {

    use ObjectTrait {
        ObjectTrait::__construct as parentConstruct;
    }

    public $foo;
    public $bar;

    public function __construct($bar, array $config = [])
    {
        $this->parentConstruct($config);
        $this->bar = $bar;
    }

}
