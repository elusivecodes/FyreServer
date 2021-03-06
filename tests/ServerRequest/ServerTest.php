<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use const
    FILTER_VALIDATE_EMAIL;

trait ServerTest
{

    public function testGetServer(): void
    {
        $this->request->setGlobals('server', [
            'test' => 'value'
        ]);

        $this->assertSame(
            'value',
            $this->request->getServer('test')
        );
    }

    public function testGetServerFilter(): void
    {
        $this->request->setGlobals('server', [
            'test' => 'value'
        ]);

        $this->assertSame(
            '',
            $this->request->getServer('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetServerAll(): void
    {
        $this->request->setGlobals('server', [
            'test' => 'value'
        ]);

        $this->assertArrayHasKey(
            'test',
            $this->request->getServer()
        );
    }

    public function testGetServerInvalid(): void
    {
        $this->assertNull(
            $this->request->getServer('invalid')
        );
    }

    public function testServerContentType(): void
    {
        $this->request->setGlobals('server', [
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->assertSame(
            'application/json',
            $this->request->getHeaderValue('Content-Type')
        );
    }

    public function testServerMethod(): void
    {
        $this->request->setGlobals('server', [
            'REQUEST_METHOD' => 'POST'
        ]);

        $this->assertSame(
            'post',
            $this->request->getMethod()
        );
    }

}
