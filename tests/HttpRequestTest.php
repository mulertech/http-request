<?php

namespace MulerTech\HttpRequest\Tests;

use MulerTech\HttpRequest\HttpRequest;
use MulerTech\HttpRequest\RequestCollector;
use MulerTech\HttpRequest\ServerRequest;
use MulerTech\HttpRequest\Session\Session;
use MulerTech\HttpRequest\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SessionTest
 * @package Tests
 * @author Sébastien Muler
 */
class HttpRequestTest extends TestCase
{
    private function sessionStart(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function testGetSession(): void
    {
        $this->sessionStart();
        $_SESSION['test'] = 'one test';
        $_SESSION['secondtest'] = ['subtest' => 'subtest value'];
        $_SESSION['thirdtest'] = ['subtest' => ['subsubtest' => 'subtest value']];
        $session = new Session();
        self::assertNull($session->get());
        self::assertNull($session->get('nope'));
        self::assertEquals('one test', $session->get('test'));
        self::assertEquals('subtest value', $session->get('secondtest', 'subtest'));
        self::assertNull($session->get('thirdtest', 'subtest', 'nope'));
    }

    public function testHasSession(): void
    {
        $this->sessionStart();
        $_SESSION['test'] = 'one test';
        $_SESSION['secondtest'] = ['subtest' => 'subtest value'];
        $_SESSION['thirdtest'] = ['subtest' => ['subsubtest' => 'subtest value']];
        $session = new Session();
        self::assertFalse($session->has());
        self::assertFalse($session->has('nope'));
        self::assertTrue($session->has('test'));
        self::assertTrue($session->has('secondtest', 'subtest'));
        self::assertFalse($session->has('thirdtest', 'subtest', 'nope'));
    }

    public function testSetSession(): void
    {
        session_write_close();
        $session = new Session();
        $session->set('test', 'one test');
        self::assertEquals('one test', $session->get('test'));
    }

    public function testDeleteSession(): void
    {
        $this->sessionStart();
        $_SESSION['test'] = 'one test';
        $session = new Session();
        $session->delete('test');
        self::assertFalse($session->has('test'));
        // Do nothing just for code coverage
        $session->delete();
    }

    public function testAddSessionWithoutIndex(): void
    {
        $this->sessionStart();
        $session = new Session();
        $this->expectExceptionMessage(
            'Class Session, function add. The index parameter (third parameter) is required.'
        );
        $session->add('key', 'value');
    }

    public function testAddWithValueExistsSession(): void
    {
        $this->sessionStart();
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
        $this->sessionStart();
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
        $this->sessionStart();
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
        $this->sessionStart();
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

    public function testGetCookie(): void
    {
        $_COOKIE['test'] = '<script>one test</script>';
        self::assertEquals('one test', HttpRequest::getCookie('test'));
        self::assertNull(HttpRequest::getCookie('nope'));
    }

    public function testCookieExists(): void
    {
        $_COOKIE['test'] = 'one test';
        self::assertTrue(HttpRequest::hasCookie('test'));
    }

    public function testGetData(): void
    {
        $_GET['test'] = '<script>one test</script>';
        self::assertEquals('one test', HttpRequest::get('test'));
        self::assertNull(HttpRequest::get('nope'));
    }

    public function testGetExists(): void
    {
        $_GET['test'] = 'one test';
        self::assertTrue(HttpRequest::has('test'));
    }

    public function testMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        self::assertEquals('GET', HttpRequest::method());
    }

    public function testPostData(): void
    {
        $_POST['test'] = '<script>one test</script>';
        self::assertEquals('one test', HttpRequest::getPost('test'));
        self::assertNull(HttpRequest::getPost('nope'));
    }

    public function testPostExists(): void
    {
        $_POST['test'] = 'one test';
        self::assertTrue(HttpRequest::hasPost('test'));
    }

    public function testRequestUri(): void
    {
        $_SERVER['REQUEST_URI'] = '/test';
        self::assertEquals('/test', HttpRequest::getUri());
    }

    public function testGetUrlWithOptions(): void
    {
        $_SERVER['PHP_SELF'] = '/test';
        $_GET['test'] = 'one';
        $_GET['test2'] = '<script>two</script>';
        self::assertEquals('/test?test=one&test2=two', HttpRequest::getUrl());
    }

    public function testGetUrlWithoutOptions(): void
    {
        $_SERVER['PHP_SELF'] = '/test';
        $_GET = [];
        self::assertEquals('/test', HttpRequest::getUrl());
    }

    public function testPostListString(): void
    {
        $_POST['test'] = 'one';
        $_POST['test2'] = '<script>two</script>';
        self::assertEquals('test:one,test2:two', HttpRequest::postListString());
    }

    public function testPostListArray(): void
    {
        $_POST['test'] = 'one';
        $_POST['test2'] = '<script>two</script>';
        self::assertEquals(['test' => 'one', 'test2' => 'two'], HttpRequest::getPostList());
    }

    public function testGetListArray(): void
    {
        $_GET['test'] = 'one';
        $_GET['test2'] = '<script>two</script>';
        self::assertEquals(['test' => 'one', 'test2' => 'two'], HttpRequest::getList());
    }

    public function testPushRequest(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->push(ServerRequest::fromGlobals());
        self::assertInstanceOf(ServerRequestInterface::class, $requestCollector->getCurrentRequest());
    }

    public function testCurrentRequestWithEmptyRequestCollector(): void
    {
        $requestCollector = new RequestCollector();
        self::assertNull($requestCollector->getCurrentRequest());
    }

    public function testPopRequest(): void
    {
        $requestCollector = new RequestCollector();
        $requestCollector->push(ServerRequest::fromGlobals());
        self::assertInstanceOf(ServerRequestInterface::class, $requestCollector->pop());
    }

    public function testPopRequestWithEmptyRequestCollector(): void
    {
        $requestCollector = new RequestCollector();
        self::assertNull($requestCollector->pop());
    }

    public function testStreamFor(): void
    {
        $stream = Utils::streamFor('test');
        self::assertEquals('test', $stream->getContents());
    }
}