<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Fyre\Server\UploadedFile;

trait FileTest
{

    public function testGetFile(): void
    {
        $this->request->setGlobals('file', [
            'test' => [
                'tmp_name' => '/tmp/tempname',
                'name' => 'test.txt',
                'type' => 'text/plain',
                'size' => 1,
                'error' => 0
            ]
        ]);

        $this->assertInstanceOf(
            UploadedFile::class,
            $this->request->getFile('test')
        );

        $this->assertEquals(
            '/tmp/tempname',
            $this->request->getFile('test')->path()
        );

        $this->assertEquals(
            'test.txt',
            $this->request->getFile('test')->clientName()
        );

        $this->assertEquals(
            'text/plain',
            $this->request->getFile('test')->clientMimeType()
        );

        $this->assertEquals(
            0,
            $this->request->getFile('test')->error()
        );
    }

    public function testGetFileDeep(): void
    {
        $this->request->setGlobals('file', [
            'test' => [
                'tmp_name' => [
                    'a' => '/tmp/tempname'
                ],
                'name' => [
                    'a' => 'test.txt'
                ],
                'type' => [
                    'a' => 'text/plain'
                ],
                'size' => [
                    'a' => 1
                ],
                'error' => [
                    'a' => 0
                ]
            ]
        ]);

        $this->assertInstanceOf(
            UploadedFile::class,
            $this->request->getFile('test.a')
        );

        $this->assertEquals(
            '/tmp/tempname',
            $this->request->getFile('test.a')->path()
        );

        $this->assertEquals(
            'test.txt',
            $this->request->getFile('test.a')->clientName()
        );

        $this->assertEquals(
            'text/plain',
            $this->request->getFile('test.a')->clientMimeType()
        );

        $this->assertEquals(
            0,
            $this->request->getFile('test.a')->error()
        );
    }

    public function testGetFileArray(): void
    {
        $this->request->setGlobals('file', [
            'test' => [
                'tmp_name' => [
                    '/tmp/tempname1',
                    '/tmp/tempname2'
                ],
                'name' => [
                    'test1.txt',
                    'test2.txt'
                ],
                'type' => [
                    'text/plain',
                    'text/plain'
                ],
                'size' => [
                    1,
                    1
                ],
                'error' => [
                    0,
                    0
                ]
            ]
        ]);

        $files = $this->request->getFile('test');

        $this->assertCount(
            2,
            $files
        );

        $this->assertInstanceOf(
            UploadedFile::class,
            $files[0]
        );

        $this->assertInstanceOf(
            UploadedFile::class,
            $files[1]
        );
    }

    public function testGetFileAll(): void
    {
        $this->request->setGlobals('file', [
            'test' => [
                'tmp_name' => '/tmp/tempname',
                'name' => 'test.txt',
                'type' => 'text/plain',
                'size' => 1,
                'error' => 0
            ]
        ]);

        $this->assertArrayHasKey(
            'test',
            $this->request->getFile()
        );
    }

    public function testGetFileInvalid(): void
    {
        $this->assertEquals(
            null,
            $this->request->getFile('invalid')
        );
    }

}
