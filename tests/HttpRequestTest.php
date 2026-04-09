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

    public function testAddWithStringValueForFirstIndex(): void
    {
        $this->sessionStart();
        $session = new Session();
        $_SESSION['test'] = 'one test';
        $session->add('key', 'value', 'test');
        self::assertEquals('value', $session->get('test', 'key'));
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

    public function testGetCookieWithNonStringNonArrayValue(): void
    {
        $_COOKIE['intval'] = 123;
        self::assertNull(HttpRequest::getCookie('intval'));
    }

    public function testGetWithNonStringNonArrayValue(): void
    {
        $_GET['intval'] = 123;
        self::assertNull(HttpRequest::get('intval'));
    }

    public function testMethodWithNonStringValue(): void
    {
        $_SERVER['REQUEST_METHOD'] = 123;
        self::assertEquals('', HttpRequest::method());
    }

    public function testGetPostWithNonStringNonArrayValue(): void
    {
        $_POST['intval'] = 123;
        self::assertNull(HttpRequest::getPost('intval'));
    }

    public function testGetPostWithArrayValue(): void
    {
        $_POST['arr'] = ['<script>a</script>', '<b>b</b>'];
        $result = HttpRequest::getPost('arr');
        self::assertIsArray($result);
        self::assertEquals(['a', 'b'], $result);
    }

    public function testGetUriWithNonStringValue(): void
    {
        $_SERVER['REQUEST_URI'] = 123;
        self::assertEquals('', HttpRequest::getUri());
    }

    public function testGetUrlWithNonStringPhpSelf(): void
    {
        $_SERVER['PHP_SELF'] = 123;
        $_GET = [];
        self::assertEquals('', HttpRequest::getUrl());
    }

    public function testHasCookieFalse(): void
    {
        unset($_COOKIE['nonexistent']);
        self::assertFalse(HttpRequest::hasCookie('nonexistent'));
    }

    public function testHasFalse(): void
    {
        unset($_GET['nonexistent']);
        self::assertFalse(HttpRequest::has('nonexistent'));
    }

    public function testHasPostFalse(): void
    {
        unset($_POST['nonexistent']);
        self::assertFalse(HttpRequest::hasPost('nonexistent'));
    }

    public function testPostListStringWithNonStringValue(): void
    {
        $_POST = ['key1' => 123, 'key2' => 'valid'];
        $result = HttpRequest::postListString();
        self::assertEquals('key1:,key2:valid', $result);
    }

    public function testGetPostListWithNonStringValue(): void
    {
        $_POST = ['key1' => 123, 'key2' => 'valid'];
        $result = HttpRequest::getPostList();
        self::assertEquals(['key1' => '', 'key2' => 'valid'], $result);
    }

    public function testGetListWithNonStringValue(): void
    {
        $_GET = ['key1' => 123, 'key2' => 'valid'];
        $result = HttpRequest::getList();
        self::assertEquals(['key1' => '', 'key2' => 'valid'], $result);
    }

    public function testGetSessionWithoutSessionStarted(): void
    {
        session_write_close();
        $session = new Session();
        self::assertNull($session->get('test'));
    }

    public function testHasSessionWithoutSessionStarted(): void
    {
        session_write_close();
        $session = new Session();
        self::assertFalse($session->has('test'));
    }

    public function testDeleteSessionWithoutSessionStarted(): void
    {
        session_write_close();
        $session = new Session();
        $session->delete('test');
        self::assertFalse($session->has('test'));
    }

    public function testDeleteSessionWithNestedIndexOnNonArrayData(): void
    {
        $this->sessionStart();
        $_SESSION['scalar'] = 'just a string';
        $session = new Session();
        $session->delete('scalar', 'nested');
        self::assertEquals('just a string', $session->get('scalar'));
    }

    public function testAddSessionWithNullFirstIndex(): void
    {
        $this->sessionStart();
        unset($_SESSION['newkey']);
        $session = new Session();
        $session->add('subkey', 'value', 'newkey');
        self::assertEquals(['subkey' => 'value'], $session->get('newkey'));
    }

    public function testGetSessionNestedWithNonArrayIntermediate(): void
    {
        $this->sessionStart();
        $_SESSION['test'] = 'just a string';
        $session = new Session();
        self::assertNull($session->get('test', 'sub'));
    }

    public function testHasSessionNestedWithNonArrayIntermediate(): void
    {
        $this->sessionStart();
        $_SESSION['test'] = 'just a string';
        $session = new Session();
        self::assertFalse($session->has('test', 'sub'));
    }

    public function testGetSessionDeepNested(): void
    {
        $this->sessionStart();
        $_SESSION['deep'] = ['level1' => ['level2' => 'found']];
        $session = new Session();
        self::assertEquals('found', $session->get('deep', 'level1', 'level2'));
    }

    public function testHasSessionDeepNested(): void
    {
        $this->sessionStart();
        $_SESSION['deep'] = ['level1' => ['level2' => 'found']];
        $session = new Session();
        self::assertTrue($session->has('deep', 'level1', 'level2'));
    }

    public function testResponseInstantiation(): void
    {
        $response = new \MulerTech\HttpRequest\Response(200);
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testServerRequestInstantiation(): void
    {
        $request = new ServerRequest('GET', '/test');
        self::assertEquals('GET', $request->getMethod());
        self::assertEquals('/test', (string) $request->getUri());
    }

    public function testStreamForWithEmptyString(): void
    {
        $stream = Utils::streamFor();
        self::assertEquals('', $stream->getContents());
    }
}