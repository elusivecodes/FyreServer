<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use function json_encode;

use const FILTER_VALIDATE_EMAIL;

trait DataTestTrait
{
    public function testGetData(): void
    {
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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

    public function testGetDataFilter(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            '',
            $request->getData('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetDataInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getData('invalid')
        );
    }

    public function testGetDataJson(): void
    {
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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
        $request = new ServerRequest([
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
}
