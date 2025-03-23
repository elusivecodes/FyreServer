<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\DateTime\DateTime;
use Fyre\Server\ServerRequest;

use function json_encode;

trait JsonTestTrait
{
    public function testGetJson(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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

    public function testGetJsonInvalid(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertNull(
            $request->getJson('invalid')
        );
    }

    public function testGetJsonType(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'body' => json_encode([
                'test' => '2024-12-31',
            ]),
        ]);

        $value = $request->getJson('test', 'date');

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
