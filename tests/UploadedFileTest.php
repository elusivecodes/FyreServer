<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use
    Fyre\FileSystem\File,
    Fyre\Server\Exceptions\ServerException,
    Fyre\Server\UploadedFile,
    PHPUnit\Framework\TestCase;

use const
    UPLOAD_ERR_NO_FILE;

final class UploadedFileTest extends TestCase
{

    public function testFile(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1'
        ]);

        $this->assertInstanceOf(
            File::class,
            $file
        );
    }

    public function testClientExtension(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'name' => 'test.txt'
        ]);

        $this->assertEquals(
            'txt',
            $file->clientExtension()
        );
    }

    public function testClientName(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'name' => 'test.txt'
        ]);

        $this->assertEquals(
            'test.txt',
            $file->clientName()
        );
    }

    public function testClientMimeType(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'type' => 'text/plain'
        ]);

        $this->assertEquals(
            'text/plain',
            $file->clientMimeType()
        );
    }

    public function testError(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'error' => UPLOAD_ERR_NO_FILE
        ]);

        $this->assertEquals(
            UPLOAD_ERR_NO_FILE,
            $file->error()
        );
    }

    public function testHasMoved(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1'
        ]);

        $this->assertEquals(
            false,
            $file->hasMoved()
        );
    }

    public function testIsValid(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1'
        ]);

        $this->assertEquals(
            false,
            $file->isValid()
        );
    }

    public function testMoveToInvalid(): void
    {
        $this->expectException(ServerException::class);

        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1'
        ]);

        $file->moveTo('tmp');
    }

}
