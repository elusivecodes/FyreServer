<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use
    Fyre\Cookie\Cookie;

trait CookieTest
{

    public function testDeleteCookie(): void
    {
        $this->assertEquals(
            $this->response,
            $this->response->deleteCookie('test')
        );

        $this->assertEquals(
            true,
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
        $this->assertEquals(
            null,
            $this->response->getCookie('invalid')
        );
    }

    public function testHasCookie(): void
    {
        $this->response->setCookie('test', 'value');

        $this->assertEquals(
            true,
            $this->response->hasCookie('test')
        );
    }

    public function testHasCookieInvalid(): void
    {
        $this->assertEquals(
            false,
            $this->response->hasCookie('invalid')
        );
    }

    public function testSetCookie(): void
    {
        $this->assertEquals(
            $this->response,
            $this->response->setCookie('test', 'value')
        );

        $this->assertEquals(
            'value',
            $this->response->getCookie('test')->getValue()
        );
    }

    public function testSetCookieOptions(): void
    {
        $this->response->setCookie('test', 'value', ['domain' => 'test.com']);

        $this->assertEquals(
            'test.com',
            $this->response->getCookie('test')->getDomain()
        );
    }

    public function testSetCookieExpires(): void
    {
        $this->response->setCookie('test', 'value', ['expires' => 60]);

        $this->assertEquals(
            false,
            $this->response->getCookie('test')->isExpired()
        );
    }

}
