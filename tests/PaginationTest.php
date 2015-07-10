<?php

namespace rockunit;

use rock\helpers\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAsSortASC()
    {
        // count "0"
        $this->assertSame([], Pagination::get(0));

        $expected = [
            'pageCount' => 1,
            'pageCurrent' => 1,
            'pageStart' => 1,
            'pageEnd' => 1,
            'pageDisplay' =>
                [
                    0 => 1,
                ],
            'pageFirst' => NULL,
            'pageLast' => NULL,
            'offset' => 0,
            'limit' => 10,
            'countMore' => 0,
        ];
        $this->assertSame($expected, Pagination::get(7, 1));

        $expected = [
            'pageCount' => 5,
            'pageCurrent' => 2,
            'pageStart' => 1,
            'pageEnd' => 5,
            'pageDisplay' =>
                [
                    0 => 1,
                    1 => 2,
                    2 => 3,
                    3 => 4,
                    4 => 5,
                ],
            'pagePrev' => 1,
            'pageNext' => 3,
            'pageFirst' => 1,
            'pageLast' => 5,
            'offset' => 10,
            'limit' => 10,
            'countMore' => 30,
        ];
        $this->assertSame($expected, Pagination::get(50, 2));

        // first page
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 1,
            'pageStart' => 1,
            'pageEnd' => 2,
            'pageDisplay' =>
                [
                    0 => 1,
                    1 => 2,
                ],
            'pageNext' => 2,
            'pageFirst' => NULL,
            'pageLast' => 2,
            'offset' => 0,
            'limit' => 5,
            'countMore' => 2,
        ];
        $this->assertSame($expected, Pagination::get(7, null, 5));
        $this->assertSame($expected, Pagination::get(7, 0, 5));
        $this->assertSame($expected, Pagination::get(7, -1, 5));
        $this->assertSame($expected, Pagination::get(7, 'foo', 5));

        // next page
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 1,
            'pageStart' => 1,
            'pageEnd' => 2,
            'pageDisplay' =>
                [
                    0 => 1,
                    1 => 2,
                ],
            'pageNext' => 2,
            'pageFirst' => NULL,
            'pageLast' => 2,
            'offset' => 0,
            'limit' => 5,
            'countMore' => 2,
        ];
        $this->assertSame($expected, Pagination::get(7, 1, 5));

        // page last
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 2,
            'pageStart' => 1,
            'pageEnd' => 2,
            'pageDisplay' =>
                [
                    0 => 1,
                    1 => 2,
                ],
            'pagePrev' => 1,
            'pageFirst' => 1,
            'pageLast' => NULL,
            'offset' => 5,
            'limit' => 5,
            'countMore' => 0,
        ];
        $this->assertSame($expected, Pagination::get(7, 7, 5));

    }

    public function testGetAsSortDESC()
    {
        // first page
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 2,
            'pageStart' => 2,
            'pageEnd' => 1,
            'pageDisplay' =>
                [
                    0 => 2,
                    1 => 1,
                ],
            'pageNext' => 1,
            'pageFirst' => NULL,
            'pageLast' => 1,
            'offset' => 0,
            'limit' => 5,
            'countMore' => 2,
        ];
        $this->assertSame($expected, Pagination::get(7, null, 5, SORT_DESC));
        $this->assertSame($expected, Pagination::get(7, 0, 5, SORT_DESC));
        $this->assertSame($expected, Pagination::get(7, -1, 5, SORT_DESC));
        $this->assertSame($expected, Pagination::get(7, 'foo', 5, SORT_DESC));

        $expected = [
            'pageCount' => 5,
            'pageCurrent' => 2,
            'pageStart' => 5,
            'pageEnd' => 1,
            'pageDisplay' =>
                [
                    0 => 5,
                    1 => 4,
                    2 => 3,
                    3 => 2,
                    4 => 1,
                ],
            'pagePrev' => 3,
            'pageNext' => 1,
            'pageFirst' => 5,
            'pageLast' => 1,
            'offset' => 30,
            'limit' => 10,
            'countMore' => 10,
        ];
        $this->assertSame($expected, Pagination::get(50, 2, 10, SORT_DESC));

        // next page
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 2,
            'pageStart' => 2,
            'pageEnd' => 1,
            'pageDisplay' =>
                [
                    0 => 2,
                    1 => 1,
                ],
            'pageNext' => 1,
            'pageFirst' => NULL,
            'pageLast' => 1,
            'offset' => 0,
            'limit' => 5,
            'countMore' => 2,
        ];
        $this->assertSame($expected, Pagination::get(7, 6, 5, SORT_DESC));

        // last page
        $expected = [
            'pageCount' => 2,
            'pageCurrent' => 1,
            'pageStart' => 2,
            'pageEnd' => 1,
            'pageDisplay' =>
                [
                    0 => 2,
                    1 => 1,
                ],
            'pagePrev' => 2,
            'pageFirst' => 2,
            'pageLast' => NULL,
            'offset' => 5,
            'limit' => 5,
            'countMore' => 0,
        ];
        $this->assertSame($expected, Pagination::get(7, 1, 5, SORT_DESC));
    }
}