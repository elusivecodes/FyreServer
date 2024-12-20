<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use const FILTER_VALIDATE_EMAIL;

trait CookieTestTrait
{
    public function testGetCookie(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'cookie' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            'value',
            $request->getCookie('test')
        );
    }

    public function testGetCookieAll(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'cookie' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'test' => 'value',
            ],
            $request->getCookie()
        );
    }

    public function testGetCookieFilter(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'cookie' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            '',
            $request->getCookie('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetCookieInvalid(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertNull(
            $request->getCookie('invalid')
        );
    }
}
