<?php

namespace rockunit;


use rock\helpers\FileHelper;
use rockunit\common\CommonTestTrait;

class FileHelperTest extends \PHPUnit_Framework_TestCase
{
    use CommonTestTrait;

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
        $this->assertTrue(FileHelper::createDirectory(ROCKUNIT_RUNTIME . '/cache/tmp'));
        $this->assertTrue(FileHelper::createDirectory(ROCKUNIT_RUNTIME . '/cache/tmp'));
    }
}