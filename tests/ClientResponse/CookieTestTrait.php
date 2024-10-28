<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use Fyre\Http\Cookie;
use Fyre\Server\ClientResponse;

use function time;

trait CookieTestTrait
{
    public function testDeleteCookie(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->deleteCookie('test');

        $this->assertNull(
            $response1->getCookie('test')
        );

        $this->assertTrue(
            $response2->getCookie('test')->isExpired()
        );
    }

    public function testGetCookie(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setCookie('test', 'value');

        $this->assertNull(
            $response1->getCookie('test')
        );

        $this->assertInstanceOf(
            Cookie::class,
            $response2->getCookie('test')
        );
    }

    public function testHasCookie(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setCookie('test', 'value');

        $this->assertFalse(
            $response1->hasCookie('test')
        );

        $this->assertTrue(
            $response2->hasCookie('test')
        );
    }

    public function testHasCookieInvalid(): void
    {
        $response = new ClientResponse();

        $this->assertFalse(
            $response->hasCookie('test')
        );
    }

    public function testSetCookie(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setCookie('test', 'value');

        $this->assertSame(
            'value',
            $response2->getCookie('test')->getValue()
        );
    }

    public function testSetCookieExpires(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setCookie('test', 'value', ['expires' => time() + 60]);

        $this->assertFalse(
            $response2->getCookie('test')->isExpired()
        );
    }

    public function testSetCookieOptions(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setCookie('test', 'value', ['domain' => 'test.com']);

        $this->assertSame(
            'test.com',
            $response2->getCookie('test')->getDomain()
        );
    }
}
