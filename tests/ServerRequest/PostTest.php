<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use const
    FILTER_VALIDATE_EMAIL;

trait PostTest
{

    public function testGetPost(): void
    {
        $this->request->setGlobals('post', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            'value',
            $this->request->getPost('test')
        );
    }

    public function testGetPostDot(): void
    {
        $this->request->setGlobals('post', [
            'test' => [
                'a' => 'value'
            ]
        ]);

        $this->assertEquals(
            'value',
            $this->request->getPost('test.a')
        );
    }

    public function testGetPostArray(): void
    {
        $this->request->setGlobals('post', [
            'test' => [
                'a' => 'value'
            ]
        ]);

        $this->assertEquals(
            [
                'a' => 'value'
            ],
            $this->request->getPost('test')
        );
    }

    public function testGetPostFilter(): void
    {
        $this->request->setGlobals('post', [
            'test' => 'value'
        ]);

        $this->assertEquals(
            '',
            $this->request->getPost('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetPostAll(): void
    {
        $this->request->setGlobals('post', [
            'test' => 'value'
        ]);

        $this->assertArrayHasKey(
            'test',
            $this->request->getPost()
        );
    }

    public function testGetPostInvalid(): void
    {
        $this->assertEquals(
            null,
            $this->request->getPost('invalid')
        );
    }

}
