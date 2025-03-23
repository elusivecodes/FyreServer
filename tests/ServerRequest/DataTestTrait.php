<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\DateTime\DateTime;
use Fyre\Server\ServerRequest;

use function json_encode;

trait DataTestTrait
{
    public function testGetData(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            'value',
            $request->getData('test')
        );
    }

    public function testGetDataAll(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'test' => 'value',
            ],
            $request->getData()
        );
    }

    public function testGetDataArray(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value',
                    ],
                ],
            ],
        ]);

        $this->assertSame(
            [
                'a' => 'value',
            ],
            $request->getData('test')
        );
    }

    public function testGetDataDot(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value',
                    ],
                ],
            ],
        ]);

        $this->assertSame(
            'value',
            $request->getData('test.a')
        );
    }

    public function testGetDataInvalid(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertNull(
            $request->getData('invalid')
        );
    }

    public function testGetDataJson(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ],
            'body' => json_encode([
                'test' => 'value',
            ]),
        ]);

        $this->assertSame(
            'value',
            $request->getData('test')
        );
    }

    public function testGetDataJsonAll(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ],
            'body' => json_encode([
                'test' => 'value',
            ]),
        ]);

        $this->assertSame(
            [
                'test' => 'value',
            ],
            $request->getData()
        );
    }

    public function testGetDataJsonArray(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ],
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
            $request->getData('test')
        );
    }

    public function testGetDataJsonDot(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ],
            'body' => json_encode([
                'test' => [
                    'a' => 'value',
                ],
            ]),
        ]);

        $this->assertSame(
            'value',
            $request->getData('test.a')
        );
    }

    public function testGetDataType(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => '2024-12-31',
                ],
            ],
        ]);

        $value = $request->getData('test', 'date');

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
