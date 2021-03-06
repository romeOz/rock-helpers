<?php

namespace rockunit;


use rock\helpers\Serialize;
use rock\helpers\SerializeException;

/**
 * @group base
 * @group helpers
 */
class SerializeTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        // PHP serializer
        $this->assertSame(Serialize::serialize(['foo', 'bar']), serialize(['foo', 'bar']));

        // Json serialozer
        $this->assertSame(Serialize::serialize(['foo', 'bar'], Serialize::SERIALIZE_JSON), json_encode(['foo', 'bar']));
    }

    public function testIs()
    {
        // true
        $this->assertTrue(Serialize::is(serialize(['foo', 'bar'])));
        $this->assertTrue(Serialize::is(serialize(false)));
        $this->assertTrue(Serialize::is(serialize('')));
        $this->assertTrue(Serialize::is(serialize([])));

        //false
        $this->assertFalse(Serialize::is('foo'));
    }

    public function testUnserialize()
    {
        // PHP
        $this->assertSame(['foo', 'bar'], Serialize::unserialize(serialize(['foo', 'bar'])));

        // Json
        $this->assertSame(['foo', 'bar'], Serialize::unserialize(json_encode(['foo', 'bar'])));

        // skip
        $this->assertSame(['foo', 'bar'], Serialize::unserialize(['foo', 'bar'], false));

        // Exception
        $this->setExpectedException(SerializeException::className());
        Serialize::unserialize(['foo', 'bar']);
    }

    public function testUnserializeRecursive()
    {
        $input = [
            'name' => 'Tom',
            'about' => [
                'discr' => json_encode(['foo' => 'text foo', 'bar' => 7])
            ],
            'test' => json_encode(['foo' => 'text foo', 'bar' => 'bar text'])
        ];
        $expected = [
            'name' => 'Tom',
            'about' =>
                [
                    'discr' =>
                        [
                            'foo' => 'text foo',
                            'bar' => 7,
                        ],
                ],
            'test' =>
                [
                    'foo' => 'text foo',
                    'bar' => 'bar text',
                ],
        ];
        $this->assertSame($expected, Serialize::unserializeRecursive($input));
    }
}
 