<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use const
    FILTER_VALIDATE_EMAIL;

trait GetTest
{

    public function testGetGet(): void
    {
        $this->request->setGlobals('get', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            'value',
            $this->request->getGet('test')
        );
    }

    public function testGetGetDot(): void
    {
        $this->request->setGlobals('get', [
            'test' => [
                'a' => 'value'
            ]
        ]);

        $this->assertEquals(
            'value',
            $this->request->getGet('test.a')
        );
    }

    public function testGetGetArray(): void
    {
        $this->request->setGlobals('get', [
            'test' => [
                'a' => 'value'
            ]
        ]);

        $this->assertEquals(
            [
                'a' => 'value'
            ],
            $this->request->getGet('test')
        );
    }

    public function testGetGetFilter(): void
    {
        $this->request->setGlobals('get', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            '',
            $this->request->getGet('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetGetAll(): void
    {
        $this->request->setGlobals('get', [
            'test' => 'value'
        ]);

        $this->assertArrayHasKey(
            'test',
            $this->request->getGet()
        );
    }

    public function testGetGetInvalid(): void
    {
        $this->assertEquals(
            null,
            $this->request->getGet('invalid')
        );
    }

}
