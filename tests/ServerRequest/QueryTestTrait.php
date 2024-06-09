<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use const FILTER_VALIDATE_EMAIL;

trait QueryTestTrait
{

    public function testGetQuery(): void
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
            $request->getQuery('test')
        );
    }

    public function testGetQueryDot(): void
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
            $request->getQuery('test.a')
        );
    }

    public function testGetQueryArray(): void
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
            $request->getQuery('test')
        );
    }

    public function testGetQueryFilter(): void
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
            $request->getQuery('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetQueryAll(): void
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
            $request->getQuery()
        );
    }

    public function testGetQueryInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getQuery('invalid')
        );
    }

}
