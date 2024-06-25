<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use function json_encode;

use const FILTER_VALIDATE_EMAIL;

trait JsonTestTrait
{
    public function testGetJson(): void
    {
        $request = new ServerRequest([
            'body' => json_encode([
                'test' => 'value',
            ]),
        ]);

        $this->assertSame(
            'value',
            $request->getJson('test')
        );
    }

    public function testGetJsonAll(): void
    {
        $request = new ServerRequest([
            'body' => json_encode([
                'test' => 'value',
            ]),
        ]);

        $this->assertSame(
            [
                'test' => 'value',
            ],
            $request->getJson()
        );
    }

    public function testGetJsonArray(): void
    {
        $request = new ServerRequest([
            'body' => json_encode([
                'test' => [
                    'a' => 'value',
                ],
            ]),
        ]);

        $this->assertSame(
            [
                'a' => 'value',
            ],
            $request->getJson('test')
        );
    }

    public function testGetJsonDot(): void
    {
        $request = new ServerRequest([
            'body' => json_encode([
                'test' => [
                    'a' => 'value',
                ],
            ]),
        ]);

        $this->assertSame(
            'value',
            $request->getJson('test.a')
        );
    }

    public function testGetJsonFilter(): void
    {
        $request = new ServerRequest([
            'body' => json_encode([
                'test' => 'value',
            ]),
        ]);

        $this->assertSame(
            '',
            $request->getJson('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetJsonInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getJson('invalid')
        );
    }
}
