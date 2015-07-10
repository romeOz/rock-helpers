<?php

namespace rockunit;


use rock\base\ObjectInterface;
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

        $instance = Instance::ensure(['class' => InstanceArgs::className(), 'foo' => 'foo test'], InstanceArgs::className(), ['bar test', 'baz test']);
        $this->assertSame('foo test', $instance->foo);
        $this->assertSame('bar test', $instance->bar);
        $this->assertSame('baz test', $instance->baz);
        $this->assertSame('test', $instance->name);

        $instance = Instance::ensure(['class' => InstanceArgsNull::className(), 'foo' => 'foo test'], InstanceArgsNull::className());
        $this->assertSame('foo test', $instance->foo);
        $this->assertNull($instance->bar);

        $instance = Instance::ensure(['class' => InstanceArgsWithoutInterface::className(), 'foo' => 'foo test'], InstanceArgsWithoutInterface::className(), [null]);
        $this->assertNull($instance->foo);
        $this->assertNull($instance->bar);
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

class Ensure
{
    use ObjectTrait;
}

class InstanceArgs implements ObjectInterface
{
    use ObjectTrait {
        ObjectTrait::__construct as parentConstruct;
    }

    public $foo;
    public $bar;
    public $baz;
    public $name;

    public function __construct($bar, $baz = null, $name = 'test', array $config = [])
    {
        $this->parentConstruct($config);
        $this->bar = $bar;
        $this->baz = $baz;
        $this->name = $name;
    }
}

class InstanceArgsNull implements ObjectInterface
{
    use ObjectTrait {
        ObjectTrait::__construct as parentConstruct;
    }

    public $foo;
    public $bar;

    public function __construct($bar = null, array $config = [])
    {
        $this->parentConstruct($config);
        $this->bar = $bar;
    }
}

class InstanceArgsWithoutInterface
{
    use ObjectTrait {
        ObjectTrait::__construct as parentConstruct;
    }

    public $foo;
    public $bar;

    public function __construct($bar = 'bar test', array $config = [])
    {
        $this->parentConstruct($config);
        $this->bar = $bar;
    }
}