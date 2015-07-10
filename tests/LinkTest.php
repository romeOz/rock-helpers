<?php

namespace rockunit;


use rock\helpers\Link;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $excpected = [
            'self' =>
                [
                    'href' => 'http://site/',
                ],
            'first' =>
                [
                    'href' => 'http://site/',
                ],
            'prev' =>
                [
                    'href' => 'http://site/',
                ],
            'next' =>
                [
                    'href' => 'http://site/?page=48',
                ],
            'last' =>
                [
                    'href' => 'http://site/?page=1',
                ],
        ];
        $this->assertSame($excpected, Link::serialize([
            Link::REL_SELF => 'http://site/',
            'first' => 'http://site/',
            'prev' => 'http://site/',
            'next' => 'http://site/?page=48',
            'last' => 'http://site/?page=1',
        ]));
    }
}
