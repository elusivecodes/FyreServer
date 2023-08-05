<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use const FILTER_VALIDATE_EMAIL;

trait ServerTestTrait
{

    public function testGetServer(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            'value',
            $request->getServer('test')
        );
    }

    public function testGetServerFilter(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            '',
            $request->getServer('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetServerAll(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            [
                'test' => 'value'
            ],
            $request->getServer()
        );
    }

    public function testGetServerInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getServer('invalid')
        );
    }

    public function testServerContentType(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'CONTENT_TYPE' => 'application/json'
                ]
            ]
        ]);

        $this->assertSame(
            'application/json',
            $request->getHeaderValue('Content-Type')
        );
    }

    public function testServerMethod(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_METHOD' => 'POST'
                ]
            ]
        ]);

        $this->assertSame(
            'post',
            $request->getMethod()
        );
    }

}
