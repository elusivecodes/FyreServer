<?php
declare(strict_types=1);

namespace Tests;

use Fyre\FileSystem\File;
use Fyre\Server\Exceptions\ServerException;
use Fyre\Server\UploadedFile;
use PHPUnit\Framework\TestCase;

use const UPLOAD_ERR_NO_FILE;

final class UploadedFileTest extends TestCase
{
    public function testClientExtension(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'name' => 'test.txt',
        ]);

        $this->assertSame(
            'txt',
            $file->clientExtension()
        );
    }

    public function testClientMimeType(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'type' => 'text/plain',
        ]);

        $this->assertSame(
            'text/plain',
            $file->clientMimeType()
        );
    }

    public function testClientName(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'name' => 'test.txt',
        ]);

        $this->assertSame(
            'test.txt',
            $file->clientName()
        );
    }

    public function testError(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
            'error' => UPLOAD_ERR_NO_FILE,
        ]);

        $this->assertSame(
            UPLOAD_ERR_NO_FILE,
            $file->error()
        );
    }

    public function testFile(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
        ]);

        $this->assertInstanceOf(
            File::class,
            $file
        );
    }

    public function testHasMoved(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
        ]);

        $this->assertFalse(
            $file->hasMoved()
        );
    }

    public function testIsValid(): void
    {
        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
        ]);

        $this->assertFalse(
            $file->isValid()
        );
    }

    public function testMoveToInvalid(): void
    {
        $this->expectException(ServerException::class);

        $file = new UploadedFile([
            'tmp_name' => '/tmp/php1',
        ]);

        $file->moveTo('tmp');
    }
}
