<?php

namespace rockunit;

use rock\helpers\Json;
use rock\helpers\JsonException;

/**
 * @group base
 * @group helpers
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        // basic data encoding
        $data = '1';
        $this->assertSame('"1"', Json::encode($data));

        // simple array encoding
        $data = [1, 2];
        $this->assertSame('[1,2]', Json::encode($data));
        $data = ['a' => 1, 'b' => 2];
        $this->assertSame('{"a":1,"b":2}', Json::encode($data));

        // simple object encoding
        $data = new \stdClass();
        $data->a = 1;
        $data->b = 2;
        $this->assertSame('{"a":1,"b":2}', Json::encode($data));

        $data = (object)null;
        $this->assertSame('{}', Json::encode($data));

        $this->assertSame(json_encode('{"a":1,"b":2}'), Json::encode(new CustomJson(['a' => 1, 'b' => 2])));
    }

    public function testDecode()
    {
        // basic data decoding
        $json = '"1"';
        $this->assertSame('1', Json::decode($json));

        // array decoding
        $json = '{"a":1,"b":2}';
        $this->assertSame(['a' => 1, 'b' => 2], Json::decode($json));

        // null
        $this->assertNull(Json::decode(null));

        // exception
        $json = '{"a":1,"b":2';
        $this->setExpectedException(JsonException::className());
        Json::decode($json);
    }

    public function testIs()
    {
        // array decoding
        $json = '{"a":1,"b":2}';
        $this->assertTrue(Json::is($json));

        $json = '{"a":1,"b":2';
        $this->assertFalse(Json::is($json));

        $this->assertFalse(Json::is(null));
    }

    public function testNormalize()
    {
        $json = <<<EOD
{"a":1,\n\r"b":2}
EOD;

        $this->assertSame('{"a":1,"b":2}', Json::normalize($json));
    }
}

class CustomJson implements \JsonSerializable
{
    protected $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return json_encode($this->data);
    }
}