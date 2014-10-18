<?php

namespace rockunit\core\helpers;;


use rock\helpers\Helper;

/**
 * @group base
 * @group helpers
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function testToType()
    {
        $this->assertSame(null, Helper::toType('null'));
        $this->assertSame(true, Helper::toType('true'));
        $this->assertSame(false, Helper::toType('false'));
        $this->assertSame(0, Helper::toType('0'));
        $this->assertSame('', Helper::toType(''));
        $this->assertSame('foo', Helper::toType('foo'));
        $this->assertSame(null, Helper::toType(null));
    }

    public function testHash()
    {
        $this->assertSame(md5('foo'), Helper::hash('foo'));
        $this->assertSame(md5(serialize(['foo'])), Helper::hash(['foo']));
        $this->assertSame(md5(json_encode(['foo'])), Helper::hash(['foo'], Helper::SERIALIZE_JSON));
    }
}
 