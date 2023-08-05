<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use const FILTER_VALIDATE_EMAIL;

trait GetTestTrait
{

    public function testGetGet(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'get' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            'value',
            $request->getGet('test')
        );
    }

    public function testGetGetDot(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'get' => [
                    'test' => [
                        'a' => 'value'
                    ]
                ]
            ]
        ]);

        $this->assertSame(
            'value',
            $request->getGet('test.a')
        );
    }

    public function testGetGetArray(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'get' => [
                    'test' => [
                        'a' => 'value'
                    ]
                ]
            ]
        ]);

        $this->assertSame(
            [
                'a' => 'value'
            ],
            $request->getGet('test')
        );
    }

    public function testGetGetFilter(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'get' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            '',
            $request->getGet('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetGetAll(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'get' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            [
                'test' => 'value'
            ],
            $request->getGet()
        );
    }

    public function testGetGetInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getGet('invalid')
        );
    }

}
