<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Helpers\Request
 */
class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];
        $_GET = [];
    }

    public function testJson_returnsEmptyArray_whenNoBody()
    {
        $result = Request::json();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testPost_returnsTrimmedValue()
    {
        $_POST['name'] = '  John  ';

        $result = Request::post('name');

        $this->assertSame('John', $result);
    }

    public function testPost_returnsDefault_whenKeyMissing()
    {
        $result = Request::post('nonexistent', 'fallback');

        $this->assertSame('fallback', $result);
    }

    public function testGet_returnsTrimmedValue()
    {
        $_GET['q'] = '  hello  ';

        $result = Request::get('q');

        $this->assertSame('hello', $result);
    }

    public function testIsMethod_returnsTrue_whenMatching()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->assertTrue(Request::isMethod('post'));
        $this->assertTrue(Request::isMethod('POST'));
    }

    public function testIsMethod_returnsFalse_whenNotMatching()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->assertFalse(Request::isMethod('POST'));
    }

    public function testIsAjax_returnsTrue_whenXmlHttpRequest()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->assertTrue(Request::isAjax());
    }

    public function testIsAjax_returnsFalse_whenNormalRequest()
    {
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);

        $this->assertFalse(Request::isAjax());
    }
}
