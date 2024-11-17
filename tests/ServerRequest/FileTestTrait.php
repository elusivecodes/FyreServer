<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;
use Fyre\Server\UploadedFile;

trait FileTestTrait
{
    public function testGetFile(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'file' => [
                    'test' => [
                        'tmp_name' => '/tmp/tempname',
                        'name' => 'test.txt',
                        'type' => 'text/plain',
                        'size' => 1,
                        'error' => 0,
                    ],
                ],
            ],
        ]);

        $file = $request->getFile('test');

        $this->assertInstanceOf(
            UploadedFile::class,
            $file
        );

        $this->assertSame(
            '/tmp/tempname',
            $file->path()
        );

        $this->assertSame(
            'test.txt',
            $file->clientName()
        );

        $this->assertSame(
            'text/plain',
            $file->clientMimeType()
        );

        $this->assertSame(
            0,
            $file->error()
        );
    }

    public function testGetFileAll(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'file' => [
                    'test' => [
                        'tmp_name' => '/tmp/tempname',
                        'name' => 'test.txt',
                        'type' => 'text/plain',
                        'size' => 1,
                        'error' => 0,
                    ],
                ],
            ],
        ]);

        $files = $request->getFile();

        $this->assertArrayHasKey(
            'test',
            $files
        );

        $this->assertInstanceOf(
            UploadedFile::class,
            $files['test']
        );
    }

    public function testGetFileArray(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'file' => [
                    'test' => [
                        'tmp_name' => [
                            '/tmp/tempname1',
                            '/tmp/tempname2',
                        ],
                        'name' => [
                            'test1.txt',
                            'test2.txt',
                        ],
                        'type' => [
                            'text/plain',
                            'text/plain',
                        ],
                        'size' => [
                            1,
                            1,
                        ],
                        'error' => [
                            0,
                            0,
                        ],
                    ],
                ],
            ],
        ]);

        $files = $request->getFile('test');

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

    public function testGetFileDeep(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'file' => [
                    'test' => [
                        'tmp_name' => [
                            'a' => '/tmp/tempname',
                        ],
                        'name' => [
                            'a' => 'test.txt',
                        ],
                        'type' => [
                            'a' => 'text/plain',
                        ],
                        'size' => [
                            'a' => 1,
                        ],
                        'error' => [
                            'a' => 0,
                        ],
                    ],
                ],
            ],
        ]);

        $file = $request->getFile('test.a');

        $this->assertInstanceOf(
            UploadedFile::class,
            $file
        );

        $this->assertSame(
            '/tmp/tempname',
            $file->path()
        );

        $this->assertSame(
            'test.txt',
            $file->clientName()
        );

        $this->assertSame(
            'text/plain',
            $file->clientMimeType()
        );

        $this->assertSame(
            0,
            $file->error()
        );
    }

    public function testGetFileInvalid(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertNull(
            $request->getFile('invalid')
        );
    }
}
