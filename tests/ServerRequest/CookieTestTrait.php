<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\DateTime\DateTime;
use Fyre\Server\ServerRequest;

trait CookieTestTrait
{
    public function testGetCookie(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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

    public function testGetCookieInvalid(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertNull(
            $request->getCookie('invalid')
        );
    }

    public function testGetCookieType(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'cookie' => [
                    'test' => '2024-12-31',
                ],
            ],
        ]);

        $value = $request->getCookie('test', 'date');

        $this->assertInstanceOf(
            DateTime::class,
            $value
        );

        $this->assertSame(
            '2024-12-31T00:00:00.000+00:00',
            $value->toISOString()
        );
    }
}
