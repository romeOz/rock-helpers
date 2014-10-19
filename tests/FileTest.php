<?php

namespace rockunit\core\helpers;


use rock\helpers\File;
use rockunit\common\CommonTrait;

/**
 * @group base
 * @group helpers
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    use CommonTrait;

    protected function setUp()
    {
        parent::setUp();
        static::clearRuntime();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->setUp();
    }

    public function testCreateDir()
    {
        $this->assertTrue(File::createDirectory(ROCKUNIT_RUNTIME . '/tmp'));
        $this->assertTrue(File::createDirectory(ROCKUNIT_RUNTIME . '/tmp'));
    }
}
 