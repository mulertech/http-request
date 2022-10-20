<?php

namespace mtphp\HttpRequest\Tests;

//Start session before use the class
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

use mtphp\HttpRequest\Session\Session;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionTest
 * @package Tests
 * @author Sébastien Muler
 */
class SessionTest extends TestCase
{

    public function testGetSession(): void
    {
        $_SESSION['test'] = 'one test';
        $session = new Session();
        self::assertEquals('one test', $session->get('test'));
    }

    public function testHasSession(): void
    {
        $_SESSION['test'] = 'one test';
        $session = new Session();
        self::assertEquals(true, $session->has('test'));
    }

    public function testSetSession(): void
    {
        $session = new Session();
        $session->set('test', 'one test');
        self::assertEquals('one test', $session->get('test'));
    }

    public function testDeleteSession(): void
    {
        $_SESSION['test'] = 'one test';
        $session = new Session();
        $session->delete('test');
        self::assertEquals(false, $session->has('test'));
    }

    public function testAddWithValueExistsSession(): void
    {
        $session = new Session();
        $_SESSION['test'] = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b'
                ]
            ]
        ];
        $expected = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoaddvalue' => 'value to add'
                ]
            ]
        ];
        $session->add('keytoaddvalue', 'value to add', 'test', 'subtest3', 'othersub');
        self::assertEquals($expected, $session->get('test'));
    }

    public function testAddWithValueExistsOnArraySession(): void
    {
        $session = new Session();
        $_SESSION['test'] = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoaddvalue' => 'value exists'
                ]
            ]
        ];
        $expected = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoaddvalue' => 'value replace old'
                ]
            ]
        ];
        $session->add('keytoaddvalue', 'value replace old', 'test', 'subtest3', 'othersub');
        self::assertEquals($expected, $session->get('test'));
    }

    public function testAddValueInSequentialArrayWithValueExistsOnArraySession(): void
    {
        $session = new Session();
        $_SESSION['test'] = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoaddvalue' => ['value exists']
                ]
            ]
        ];
        $expected = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoaddvalue' => ['value exists', 'other value']
                ]
            ]
        ];
        $session->add('keytoaddvalue', 'other value', 'test', 'subtest3', 'othersub');
        self::assertEquals($expected, $session->get('test'));
    }

    public function testRemoveKeyWithValueExistsOnArraySession(): void
    {
        $session = new Session();
        $_SESSION['test'] = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b',
                    'keytoremovevalue' => 'value exists'
                ]
            ]
        ];
        $expected = [
            'subtest1' => [
                'subsubtest1' => 'a value',
                'subsubtest2' => 'b value',
                'subsubtest3' => 'c value'
            ],
            'subtest2' => [
                'subsubsecondtest1' => 'another value a',
                'subsubsecondtest2' => 'another value b',
                'subsubsecondtest3' => 'another value c',
                'subsubsecondtest4' => 'another value d',
            ],
            'subtest3' => [
                'othersub' => [
                    'subsubsub1' => 'value a',
                    'subsubsub2' => 'value b'
                ]
            ]
        ];
        $session->delete('test', 'subtest3', 'othersub', 'keytoremovevalue');
        self::assertEquals($expected, $session->get('test'));
    }

}