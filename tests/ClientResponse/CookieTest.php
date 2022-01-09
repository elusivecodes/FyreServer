<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use
    Fyre\Cookie\Cookie;

trait CookieTest
{

    public function testDeleteCookie(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->deleteCookie('test')
        );

        $this->assertTrue(
            $this->response->getCookie('test')->isExpired()
        );
    }

    public function testGetCookie(): void
    {
        $this->response->setCookie('test', 'value');

        $this->assertInstanceOf(
            Cookie::class,
            $this->response->getCookie('test')
        );
    }

    public function testGetCookieInvalid(): void
    {
        $this->assertNull(
            $this->response->getCookie('invalid')
        );
    }

    public function testHasCookie(): void
    {
        $this->response->setCookie('test', 'value');

        $this->assertTrue(
            $this->response->hasCookie('test')
        );
    }

    public function testHasCookieInvalid(): void
    {
        $this->assertFalse(
            $this->response->hasCookie('invalid')
        );
    }

    public function testSetCookie(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->setCookie('test', 'value')
        );

        $this->assertSame(
            'value',
            $this->response->getCookie('test')->getValue()
        );
    }

    public function testSetCookieOptions(): void
    {
        $this->response->setCookie('test', 'value', ['domain' => 'test.com']);

        $this->assertSame(
            'test.com',
            $this->response->getCookie('test')->getDomain()
        );
    }

    public function testSetCookieExpires(): void
    {
        $this->response->setCookie('test', 'value', ['expires' => 60]);

        $this->assertFalse(
            $this->response->getCookie('test')->isExpired()
        );
    }

}
