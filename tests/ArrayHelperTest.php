<?php

namespace rockunit;

use rock\helpers\ArrayException;
use rock\helpers\ArrayHelper;
use rock\helpers\Json;
use rock\helpers\Serialize;

/**
 * @group base
 * @group helpers
 */
class ArrayHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group php
     * @dataProvider providerToArray
     */
    public function testToArray($expected, $actual, $only = [], $exclude = [])
    {
        $this->assertSame(ArrayHelper::toArray($expected, $only, $exclude, true), $actual);
    }

    public function providerToArray()
    {
        return [
//            [
//                [
//                    (object)['id', 'title', 'title1'],
//                    (object)['id', 'title', 'title2'],
//                ],
//                [
//                    [],
//                    [],
//                ],
//            ],
            [
                [
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
                [
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
            ],
            [
                [
                    'names' => Json::encode(['Tom', 'Jane']),
                    'email' => 'test@site.com',
                ],
                [
                    'names' => ['Tom', 'Jane'],
                    'email' => 'test@site.com',
                ],
            ],
            [
                [
                    'orders' => ['names' => Json::encode(['Tom', 'Jane'])],
                    'email' => 'test@site.com',
                ],
                [
                    'orders' => ['names' => ['Tom', 'Jane']],
                    'email' => 'test@site.com',
                ],
            ],
            [
                (object)[
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
                [
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
            ],
            [
                (object)[
                    'names' => Json::encode(['Tom', 'Jane']),
                    'email' => 'test@site.com',
                ],
                [
                    'names' => ['Tom', 'Jane'],
                    'email' => 'test@site.com',
                ],
            ],
            [
                (object)[
                    'name' => 'Tom',
                    'emails' => ['test@site.com', 'tom@site.com'],
                ],
                [
                    'name' => 'Tom',
                    'emails' => ['test@site.com', 'tom@site.com'],
                ],
            ],
            [
                (object)[
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],

            [
                Serialize::serialize([
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ], Serialize::SERIALIZE_JSON),
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],
            [
                Serialize::serialize([
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ]),
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],

            [
                Json::decode(Json::encode([
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ]), false),
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],
            [
                (object)[
                    'name' => 'Tom',
                    'orders' => (object)[
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],
            [
                (object)[
                    'name' => 'Tom',
                    'orders' => (object)[
                        'order_1' => Json::encode(['name' => 'name_1']),
                        'order_2' => Json::encode(['name' => 'name_2'])
                    ],
                ],
                [
                    'name' => 'Tom',
                    'orders' => [
                        'order_1' => ['name' => 'name_1'],
                        'order_2' => ['name' => 'name_2']
                    ],
                ],
            ],
            [
                [
                    'name' => 'Tom',
                    'email' => (object)'test@site.com',
                ],
                [
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
            ],
            [
                [
                    'name' => (object)'Tom',
                    'email' => (object)'test@site.com',
                ],
                [
                    'name' => 'Tom',
                    'email' => 'test@site.com',
                ],
            ],
            [
                [
                    'names' => ['Tom', 'Jane'],
                    'emails' => ['test@site.com', 'jane@site.com'],
                ],
                [
                    'names' => ['Tom', 'Jane'],
                    'emails' => ['test@site.com', 'jane@site.com'],
                ],
            ],
            [
                [
                    ['id' => 1, 'title' => 'title1'],
                    ['id' => 2, 'title' => 'title2'],
                ],
                [
                    ['id' => 1, 'title' => 'title1'],
                    ['id' => 2, 'title' => 'title2'],
                ],
            ],
            [
                [
                    (object)['id' => 1, 'title' => 'title1'],
                    (object)['id' => 2, 'title' => 'title2'],
                ],
                [
                    ['id' => 1, 'title' => 'title1'],
                    ['id' => 2, 'title' => 'title2'],
                ],
            ],

        ];
    }

    /**
     * @group hhvm
     * @dataProvider providerToArrayHHVM
     */
    public function testToArrayHHVM($expected, $actual, $only = [], $exclude = [])
    {
        $this->assertSame(ArrayHelper::toArray($expected, $only, $exclude, true), $actual);
    }

    public function providerToArrayHHVM()
    {
        $result = $this->providerToArray();
        $result[0] = [
            [
                (object)['id', 'title', 'title1'],
                (object)['id', 'title', 'title2'],
            ],
            [
                ['id', 'title', 'title1'],
                ['id', 'title', 'title2'],
            ],
        ];
        return $result;
    }

    public function testToSingle()
    {
        $this->assertEquals(
            ArrayHelper::toSingle(
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2'],
                    'cc' => [
                        'aa' =>
                            ['gg' => 'text3']
                    ]
                ]
            ),
            ['aa' => 'text', 'bb.aa' => 'text2', 'cc.aa.gg' => 'text3']
        );
    }


    /**
     * @dataProvider providerToMulti
     */
    public function testToMulti($expected, $actual, $recursive = false)
    {
        $this->assertEquals(ArrayHelper::toMulti($expected, '.', $recursive), $actual);
    }

    public function providerToMulti()
    {
        return [
            [
                ['aa' => 'text', 'bb.aa' => 'text2', 'cc.aa.gg' => ['aa.bb' => 'text3']],
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2'],
                    'cc' => [
                        'aa' =>
                            ['gg' => ['aa.bb' => 'text3']]
                    ]
                ],
            ],
            [
                ['aa' => 'text', 'bb.aa' => 'text2', 'cc.aa.gg' => ['aa.bb' => 'text3']],
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2'],
                    'cc' => [
                        'aa' =>
                            ['gg' => ['aa' => ['bb' => 'text3']]]
                    ]
                ],
                true
            ],
            [
                ['aa' => 'text', 'bb.aa' => 'text2', 'cc.aa.gg' => ['aa' => ['aa.bb' => 'text3']]],
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2'],
                    'cc' => [
                        'aa' =>
                            ['gg' => ['aa' => ['aa' => ['bb' => 'text3']]]]
                    ]
                ],
                true
            ],

            [
                ['aa' => 'text', 'bb.aa' => 'text2', 'cc.aa.gg' => ['aa' => ['aa.bb' => 'text3']], ['dd.bb' => ['aa.cc' => 'text3']]],
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2'],
                    'cc' => [
                        'aa' =>
                            ['gg' => ['aa' => ['aa' => ['bb' => 'text3']]]]
                    ],
                    ['dd' => ['bb' => ['aa' => ['cc' => 'text3']]]]
                ],
                true
            ],
            [
                ['aa' => 'text', 'bb.aa' => 'text2', 'bb.cc' => ['dd' => ['gg.aa' => 'text3']]],
                [
                    'aa' => 'text',
                    'bb' => ['aa' => 'text2', 'cc' => ['dd' => ['gg' => ['aa' => 'text3']]]],

                ],
                true
            ],
        ];
    }


    /**
     * @dataProvider providerRemove
     */
    public function testRemove($expected, $actual, $keys)
    {
        $this->assertEquals(ArrayHelper::removeValue($expected, $keys), $actual);
    }

    public function providerRemove()
    {
        return [
            [
                ['type' => 'A', 'options' => [1, 2]],
                ['options' => [1, 2]],
                'type'
            ],

            [
                ['type' => 'A', 'options' => [1, 2]],
                ['options' => [1, 2]],
                ['type']
            ],
            [
                ['type' => 'A', 'options' => ['name' => 'option_1', 'params' => ['param1', 'param2']]],
                ['type' => 'A', 'options' => ['name' => 'option_1']],
                'options.params'
            ],

            [
                ['type' => 'A', 'options' => ['name' => 'option_1', 'params' => ['param1', 'param2']]],
                ['type' => 'A', 'options' => ['name' => 'option_1', 'params' => ['param1']]],
                ['options', 'params', 1]
            ],
        ];
    }


    /**
     * @dataProvider providerMove
     */
    public function testMove($expected, $actual, $key, $move = ArrayHelper::MOVE_HEAD)
    {
        $this->assertSame(ArrayHelper::moveElement($expected, $key, $move), $actual);
    }

    public function providerMove()
    {
        return [

            [
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                ['title' => 'text3', 'id' => 1, 'params' => ['param_1', 'param_2']],
                'title'
            ],

            [
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                ['title' => 'text3', 'params' => ['param_1', 'param_2'], 'id' => 1],
                'id',
                ArrayHelper::MOVE_TAIL
            ],

            [
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                'params',
                ArrayHelper::MOVE_TAIL
            ],
            [
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                ['id' => 1, 'title' => 'text3', 'params' => ['param_1', 'param_2']],
                'id',
                ArrayHelper::MOVE_HEAD
            ],
        ];
    }

    public function testSetValue()
    {
        $array = [
            'name' => 'test',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'romeo',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin' => [
                'lastname' => 'romeo',
            ],
        ];

        // update
        $this->assertSame('Tom', ArrayHelper::setValue($array, 'post.author.name', 'Tom')['post']['author']['name']);

        // set
        $this->assertSame('Sawyer', ArrayHelper::setValue($array, 'post.author.lastname', 'Sawyer')['post']['author']['lastname']);

        // empty keys
        $this->assertSame($array, ArrayHelper::setValue($array, '', 'Sawyer'));
    }

    public function testUpdateValue()
    {
        $array = [
            'name' => 'test',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'romeo',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin' => [
                'lastname' => 'romeo',
                'firstname' => null
            ],
        ];
        $callback = function ($value) {
            if ($value == 'romeo') {
                return 'Tom';
            }
            return $value;
        };

        $this->assertSame('Tom', ArrayHelper::updateValue($array, ['post', 'author', 'name'], $callback)['post']['author']['name']);

        // condition is false
        $this->assertSame('1337', ArrayHelper::updateValue($array, ['post', 'author', 'profile', 'title'], $callback)['post']['author']['profile']['title']);

        $this->assertSame('Tom', ArrayHelper::updateValue($array, ['admin', 'firstname'], function () {
            return 'Tom';
        })['admin']['firstname']);

        // unknown
        $this->assertSame($array, ArrayHelper::updateValue($array, [], $callback));
        $this->assertSame($array, ArrayHelper::updateValue($array, ['admin', 'firstname', 'unknown'], function () {
            return 'Tom';
        }, false));
    }

    public function testUpdateValueThrowException()
    {
        $array = [
            'name' => 'test',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'romeo',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin' => [
                'lastname' => 'romeo',
            ],
        ];
        $callback = function ($value) {
            if ($value == 'romeo') {
                return 'Tom';
            }
            return $value;
        };
        $this->setExpectedException(ArrayException::className());
        ArrayHelper::updateValue($array, ['post', 'author', 'lastname'], $callback);
    }

    /**
     * @dataProvider valueProvider
     *
     * @param $key
     * @param $expected
     * @param null $default
     */
    public function testGetValue($key, $expected, $default = null)
    {
        $array = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'romeo',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin.firstname' => 'Sergey',
            'admin.lastname' => 'Galka',
            'admin' => [
                'lastname' => 'romeo',
            ],
        ];

        $this->assertEquals($expected, ArrayHelper::getValue($array, $key, $default));
    }


    public function valueProvider()
    {
        return [
            ['name', 'test'],
            ['noname', null],
            ['noname', 'test', 'test'],
            ['post.id', 5],
            [['post', 'id'], 5],
            ['post.id', 5, 'test'],
            ['nopost.id', null],
            ['nopost.id', 'test', 'test'],
            ['post.author.name', 'romeo'],
            ['post.author.noname', null],
            ['post.author.noname', 'test', 'test'],
            ['post.author.profile.title', '1337'],
            ['admin.firstname', 'Sergey'],
            ['admin.firstname', 'Sergey', 'test'],
            ['admin.lastname', 'Galka'],
            [
                function ($array, $defaultValue) {
                    return $array['date'] . $defaultValue;
                },
                '31-12-2113test',
                'test'
            ],
            [[], [
                'name' => 'test',
                'date' => '31-12-2113',
                'post' => [
                    'id' => 5,
                    'author' => [
                        'name' => 'romeo',
                        'profile' => [
                            'title' => '1337',
                        ],
                    ],
                ],
                'admin.firstname' => 'Sergey',
                'admin.lastname' => 'Galka',
                'admin' => [
                    'lastname' => 'romeo',
                ],
            ]],
        ];
    }

    public function testGetValueEmpty()
    {
        $this->assertSame('unknown', ArrayHelper::getValue([], 'name', 'unknown'));
    }

    public function testGetValueAsObject()
    {
        $object = new \stdClass();
        $subobject = new \stdClass();
        $subobject->bar = 'test';
        $object->foo = $subobject;
        $object->baz = 'text';
        $this->assertSame(ArrayHelper::getValue($object, 'foo.bar'), 'test');
        $this->assertSame(ArrayHelper::getValue($object, ['foo', 'bar']), 'test');
        $this->assertSame(ArrayHelper::getValue($object, 'baz'), 'text');
    }

    public function testSearch()
    {
        $input = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => true,
                    '_2.1.2' => 7.7,
                    '_2.1.3' => 'foo text',
                ]
            ],
            '_3' => null,
            '_4' => 7.7,
            '_5' => 'foo text',
        ];


        $this->assertArrayHasKey('_2.1.2', ArrayHelper::search(7.7, $input, $keys));
        $this->assertSame(['_2', '_2.1', '_2.1.2',], $keys);

        $keys = [];
        $this->assertArrayHasKey('_1.1', ArrayHelper::search(null, $input, $keys));
        $this->assertSame(['_1', '_1.1'], $keys);

        // not
        $keys = [];
        $this->assertEmpty(ArrayHelper::search('unknown', $input, $keys));
        $this->assertEmpty($keys);

        // regexp
        $keys = [];
        $this->assertArrayHasKey('_2.1.3', ArrayHelper::pregSearch('/text$/', $input, $keys));
        $this->assertSame(['_2', '_2.1', '_2.1.3',], $keys);
    }

    public function testContains()
    {
        $input = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => true,
                    '_2.1.2' => 7.7,
                    '_2.1.3' => 'foo text',
                ]
            ],
            '_3' => null,
            '_4' => 7.7,
            '_5' => 'foo text',
        ];

        $this->assertTrue(ArrayHelper::contains(7.7, $input));
        $this->assertFalse(ArrayHelper::contains('unknown', $input));
    }

    public function testSearchKey()
    {
        $input = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => true,
                    'name' => 'Tom',
                    'lastname' => 'Sawyer',
                ]
            ],
            '_3' => null,
            'city' => 7.7,
            'name' => 'Huckleberry',
            'lastname' => 'Finn'
        ];

        $this->assertContains('Tom', ArrayHelper::searchByKey('name', $input, $keys));
        $this->assertSame(['_2', '_2.1', 'name',], $keys);

        // not
        $keys = [];
        $this->assertEmpty(ArrayHelper::searchByKey('unknown', $input, $keys));
        $this->assertEmpty($keys);

        // regexp
        $keys = [];
        $this->assertContains('Sawyer', ArrayHelper::pregSearchByKey('/^last/', $input, $keys));
        $this->assertSame(['_2', '_2.1', 'lastname',], $keys);
    }


    public function testSearchAll()
    {
        $input = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => true,
                    '_2.1.2' => 7.7,
                    '_2.1.3' => 'foo text',
                ]
            ],
            '_3' => null,
            '_4' => 7.7,
            '_5' => 'foo text',
        ];


        $actual = ArrayHelper::searchAll(7.7, $input, $keys);
        $this->assertContains('_2.1.2', $actual[0]);
        $this->assertContains('_4', $actual[1]);
        $this->assertSame(['_2', '_2.1', '_2.1.2',], $keys[0]['_2.1.2']);
        $this->assertSame(['_4',], $keys[1]['_4']);

        // not
        $keys = [];
        $this->assertEmpty(ArrayHelper::searchAll('unknown', $input, $keys));
        $this->assertEmpty($keys);

        // regexp
        $keys = [];
        $actual = ArrayHelper::pregSearchAll('/text$/', $input, $keys);
        $this->assertContains('_2.1.3', $actual[0]);
        $this->assertContains('_5', $actual[1]);
        $this->assertSame(['_2', '_2.1', '_2.1.3',], $keys[0]['_2.1.3']);
        $this->assertSame(['_5',], $keys[1]['_5']);
    }

    public function testSearchAllKeys()
    {
        $input = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => true,
                    'name' => 'Tom',
                    'lastname' => 'Sawyer',
                ]
            ],
            '_3' => null,
            'city' => 7.7,
            'name' => 'Huckleberry',
            'lastname' => 'Finn'
        ];

        $actual = ArrayHelper::searchAllByKey('name', $input, $keys);
        $this->assertContains('Tom', $actual[0]);
        $this->assertContains('Huckleberry', $actual[1]);
        $this->assertSame(['_2', '_2.1', 'name',], $keys[0]['name']);
        $this->assertSame(['name'], $keys[1]['name']);

        // not
        $keys = [];
        $this->assertEmpty(ArrayHelper::searchAllByKey('unknown', $input, $keys));
        $this->assertEmpty($keys);

        // regexp
        $keys = [];
        $actual = ArrayHelper::pregSearchAllByKey('/^last/', $input, $keys);
        $this->assertContains('Sawyer', $actual[0]);
        $this->assertContains('Finn', $actual[1]);
        $this->assertSame(['_2', '_2.1', 'lastname',], $keys[0]['lastname']);
        $this->assertSame(['lastname',], $keys[1]['lastname']);
    }

    public function testDepth()
    {
        $input = [
            '_1' => [
                '_1.1' => 'foo'
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => ['test']
                ]
            ]
        ];

        $this->assertSame(3, ArrayHelper::depth($input));
        $this->assertSame(1, ArrayHelper::depth($input, true));
    }

    public function testToType()
    {
        $input = [
            '_1' => [
                '_1.1' => 'false'
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => ['true'],
                    '_2.1.2' => ['7.7'],
                ]
            ],
            '_3' => 'null',
            '_4' => 7
        ];

        $expected = [
            '_1' => [
                '_1.1' => false
            ],
            '_2' => [
                '_2.1' => [
                    '_2.1.1' => [true],
                    '_2.1.2' => [7.7],
                ]
            ],
            '_3' => null,
            '_4' => 7
        ];

        $this->assertSame($expected, ArrayHelper::toType($input));
    }

    public function testGroup()
    {
        $input = [
            ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
            ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
            ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
        ];

        $expected = [
            '123' => 'aaa',
            '124' => 'bbb',
            '345' => 'ccc',
        ];
        $this->assertSame($expected, ArrayHelper::group($input, 'id', ['name']));

        $expected = [
            'x' => [
                '123' => 'aaa',
                '124' => 'bbb',
            ],
            'y' => [
                '345' => 'ccc',
            ],
        ];
        $this->assertSame($expected, ArrayHelper::group($input, 'id', 'name', 'class'));
    }

    public function testFilterColumn()
    {
        $input = [
            '_1' => ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
            '_2' => ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
            '_3' => ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
        ];
        $expected = [
            '_1' => 'aaa',
            '_2' => 'bbb',
            '_3' => 'ccc',
        ];
        $this->assertSame($expected, ArrayHelper::filterColumn($input, 'name'));

        $expected = [
            123 =>
                [
                    'name' => 'aaa',
                    'class' => 'x',
                ],
            124 =>
                [
                    'name' => 'bbb',
                    'class' => 'x',
                ],
            345 =>
                [
                    'name' => 'ccc',
                    'class' => 'y',
                ],
        ];
        $this->assertSame($expected, ArrayHelper::filterColumn($input, ['name', 'class'], 'id'));
    }

    public function testConcatKeyValue()
    {
        $input = [
            '123' => 'aaa',
            '124' => 'bbb',
        ];

        $this->assertSame(['123=aaa', '124=bbb'], ArrayHelper::concatKeyValue($input));
    }

    public function testKeyExists()
    {
        $array = [
            'a' => 1,
            'B' => 2,
        ];
        $this->assertTrue(ArrayHelper::keyExists('a', $array));
        $this->assertFalse(ArrayHelper::keyExists('b', $array));
        $this->assertTrue(ArrayHelper::keyExists('B', $array));
        $this->assertFalse(ArrayHelper::keyExists('c', $array));

        $this->assertTrue(ArrayHelper::keyExists('a', $array, false));
        $this->assertTrue(ArrayHelper::keyExists('b', $array, false));
        $this->assertTrue(ArrayHelper::keyExists('B', $array, false));
        $this->assertFalse(ArrayHelper::keyExists('c', $array, false));
    }

    public function testMultisort()
    {
        // single key
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArrayHelper::multisort($array, 'name');
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArrayHelper::multisort($array, ['name', 'age']);
        $this->assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 2], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 3], $array[2]);

        // case-insensitive
        $array = [
            ['name' => 'a', 'age' => 3],
            ['name' => 'b', 'age' => 2],
            ['name' => 'B', 'age' => 4],
            ['name' => 'A', 'age' => 1],
        ];

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[1]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[2]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[3]);

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING | SORT_FLAG_CASE, SORT_REGULAR]);
        $this->assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        $this->assertEquals(['name' => 'a', 'age' => 3], $array[1]);
        $this->assertEquals(['name' => 'b', 'age' => 2], $array[2]);
        $this->assertEquals(['name' => 'B', 'age' => 4], $array[3]);
    }

    public function testMerge()
    {
        $a = [
            'name' => 'Rock',
            'version' => 'beta',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];
        $c = [
            'version' => '2.0',
            'options' => [
                'namespace' => true,
            ],
            'features' => [
                'debug',
            ],
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'name' => 'Rock',
            'version' => '2.0',
            'options' => [
                'namespace' => true,
                'unittest' => true,
            ],
            'features' => [
                'mvc',
                'gii',
                'debug',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testIndex()
    {
        $array = [
            ['id' => '123', 'data' => 'abc'],
            ['id' => '345', 'data' => 'def'],
        ];
        $result = ArrayHelper::index($array, 'id');
        $this->assertEquals([
            '123' => ['id' => '123', 'data' => 'abc'],
            '345' => ['id' => '345', 'data' => 'def'],
        ], $result);

        $result = ArrayHelper::index($array, function ($element) {
            return $element['data'];
        });
        $this->assertEquals([
            'abc' => ['id' => '123', 'data' => 'abc'],
            'def' => ['id' => '345', 'data' => 'def'],
        ], $result);
    }

    public function testGetColumn()
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];
        $result = ArrayHelper::getColumn($array, 'id');
        $this->assertEquals(['a' => '123', 'b' => '345'], $result);
        $result = ArrayHelper::getColumn($array, 'id', false);
        $this->assertEquals(['123', '345'], $result);

        $result = ArrayHelper::getColumn($array, function ($element) {
            return $element['data'];
        });
        $this->assertEquals(['a' => 'abc', 'b' => 'def'], $result);
        $result = ArrayHelper::getColumn($array, function ($element) {
            return $element['data'];
        }, false);
        $this->assertEquals(['abc', 'def'], $result);
    }

    public function testIntersectByKeys()
    {
        $this->assertSame(ArrayHelper::intersectByKeys(['foo' => 'foo', 'bar' => 'bar'], ['bar']), ['bar' => 'bar']);
    }

    public function testDiffByKeys()
    {
        $this->assertSame(ArrayHelper::diffByKeys(['foo' => 'foo', 'bar' => 'bar'], ['bar']), ['foo' => 'foo']);
    }

    public function testMap()
    {
        $callback = function () {
            return 'test';
        };
        $this->assertSame(ArrayHelper::map(['foo' => 'foo', 'bar' => 'bar'], $callback, false, 1), ['foo' => 'test', 'bar' => 'bar']);

        // recursive
        $this->assertSame(ArrayHelper::map(['foo' => 'foo', 'bar' => ['baz' => 'baz']], $callback, true), ['foo' => 'test', 'bar' => ['baz' => 'test']]);
    }

    public function testIsAssociative()
    {
        $this->assertFalse(ArrayHelper::isAssociative('test'));
        $this->assertFalse(ArrayHelper::isAssociative([]));
        $this->assertFalse(ArrayHelper::isAssociative([1, 2, 3]));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test']));
        $this->assertFalse(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3]));
        $this->assertTrue(ArrayHelper::isAssociative(['name' => 1, 'value' => 'test', 3], false));
    }

    public function testIsIndexed()
    {
        $this->assertFalse(ArrayHelper::isIndexed('test'));
        $this->assertTrue(ArrayHelper::isIndexed([]));
        $this->assertTrue(ArrayHelper::isIndexed([1, 2, 3]));
        $this->assertTrue(ArrayHelper::isIndexed([2 => 'a', 3 => 'b']));
        $this->assertFalse(ArrayHelper::isIndexed([2 => 'a', 3 => 'b'], true));
    }

    public function testHtmlEncode()
    {
        $array = [
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ]
        ];
        $this->assertEquals([
            'abc' => '123',
            '<' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ]
        ], ArrayHelper::htmlEncode($array));
        $this->assertEquals([
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '&lt;&gt;' => 'a&lt;&gt;b',
                '23' => true,
            ]
        ], ArrayHelper::htmlEncode($array, false));
    }

    public function testHtmlDecode()
    {
        $array = [
            'abc' => '123',
            '&lt;' => '&gt;',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a&lt;&gt;b',
                '23' => true,
            ]
        ];
        $this->assertEquals([
            'abc' => '123',
            '&lt;' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ]
        ], ArrayHelper::htmlDecode($array));
        $this->assertEquals([
            'abc' => '123',
            '<' => '>',
            'cde' => false,
            3 => 'blank',
            [
                '<>' => 'a<>b',
                '23' => true,
            ]
        ], ArrayHelper::htmlDecode($array, false));
    }
}