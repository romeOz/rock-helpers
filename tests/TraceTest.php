<?php

namespace rockunit;


use rock\helpers\Trace;


/**
 * @group base
 * @group helpers
 */
class TraceTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Trace::removeAll();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Trace::removeAll();
    }

    public function testTrace()
    {
        $this->assertEmpty(Trace::getIterator('test'));
        Trace::trace('test', 'test...');
        $this->assertSame(count(Trace::get('test')), 1);

        // token as Array
        Trace::trace('test', ['foo' => 'test', 'exception' => 'text']);
        $this->assertSame(count(Trace::get('test')), 2);

        // getIterator
        $iterator = Trace::getIterator('test');
        $this->assertSame($iterator->current(), 'test...');
        $iterator->next();
        $this->assertSame(
            $iterator->current(),
            array(
                'foo' => 'test',
                'exception' => 'text',
            )
        );
    }

    public function testProfile()
    {
        $token = ['foo' => 'test', 'exception' => 'text'];
        Trace::beginProfile('test', $token);
        $iterator = Trace::getIterator('test');

        $this->assertSame($iterator->count(), 1);
        $this->assertSame(count($iterator->current()), 3);
        sleep(1);
        Trace::endProfile('test', $token);
        $iterator = Trace::getIterator('test');
        $this->assertSame($iterator->count(), 1);
        $this->assertSame((int)$iterator->current()['time'], 1);

        Trace::removeAll();

        // already created
        Trace::trace('test', $token);
        $iterator = Trace::getIterator('test');
        $this->assertArrayNotHasKey('time', $iterator->current());
        Trace::beginProfile('test', $token);
        $iterator = Trace::getIterator('test');

        $this->assertSame($iterator->count(), 1);
        $this->assertSame(count($iterator->current()), 3);
        sleep(1);
        Trace::endProfile('test', $token);
        $iterator = Trace::getIterator('test');
        $this->assertSame($iterator->count(), 1);
        $this->assertSame((int)$iterator->current()['time'], 1);

        Trace::removeAll();

        // token as string
        $token = 'test...';
        Trace::beginProfile('test', $token);
        $iterator = Trace::getIterator('test');

        $this->assertSame($iterator->count(), 1);
        $this->assertSame(count($iterator->current()), 2);
        $this->assertArrayHasKey('msg', $iterator->current());
        sleep(1);
        Trace::endProfile('test', $token);
        $iterator = Trace::getIterator('test');
        $this->assertSame($iterator->count(), 1);
        $this->assertSame((int)$iterator->current()['time'], 1);
    }

    public function testIncrement()
    {
        $token = ['foo' => 'test', 'exception' => 'text'];
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 1);
        Trace::increment('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 3);

        Trace::removeAll();

        // already created
        Trace::trace('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 1);
        Trace::increment('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 3);

        Trace::removeAll();

        // token as string
        $token = 'test...';
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 2);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 1);
        Trace::increment('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 2);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 3);
    }


    public function testDecrement()
    {
        $token = ['foo' => 'test', 'exception' => 'text'];
        Trace::decrement('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 0);
        Trace::increment('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        Trace::decrement('test', $token);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 1);

        Trace::removeAll();

        // already created
        $token['increment'] = 5;
        Trace::trace('test', $token);
        Trace::decrement('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 4);
        Trace::decrement('test', $token);
        Trace::decrement('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 3);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 2);

        Trace::removeAll();

        // token as string
        $token = 'test...';
        Trace::decrement('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 2);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 0);
        Trace::increment('test', $token);
        Trace::increment('test', $token);
        $this->assertSame(count(Trace::getIterator('test')->current()), 2);
        Trace::decrement('test', $token);
        $this->assertSame(Trace::getIterator('test')->current()['increment'], 1);
    }

    public function testHas()
    {
        // true
        Trace::trace('test', 'test...');
        $this->assertTrue(Trace::exists('test'));

        // count
        $this->assertSame(Trace::count('test'), 1);
        $this->assertSame(Trace::count(), 1);

        Trace::remove('test');
        // false
        $this->assertFalse(Trace::exists('test'));

        $this->assertSame(Trace::count('test'), 0);
        $this->assertSame(Trace::count(), 0);
    }
}
 