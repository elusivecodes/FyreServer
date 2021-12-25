<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use const
    FILTER_VALIDATE_EMAIL;

trait CookieTest
{

    public function testGetCookie(): void
    {
        $this->request->setGlobals('cookie', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            'value',
            $this->request->getCookie('test')
        );
    }

    public function testGetCookieFilter(): void
    {
        $this->request->setGlobals('cookie', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            '',
            $this->request->getCookie('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetCookieAll(): void
    {
        $this->request->setGlobals('cookie', [
            'test' => 'value'
        ]);

        $this->assertArrayHasKey(
            'test',
            $this->request->getCookie()
        );
    }

    public function testGetCookieInvalid(): void
    {
        $this->assertEquals(
            null,
            $this->request->getCookie('invalid')
        );
    }

}
