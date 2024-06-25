<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Server\ClientResponse;
use Fyre\Server\DownloadResponse;
use Fyre\Server\Exceptions\ServerException;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

final class DownloadResponseTest extends TestCase
{
    public function testFilename(): void
    {
        $response = new DownloadResponse('tests/Mock/test.txt', [
            'filename' => 'file.txt',
        ]);

        $this->assertSame(
            'attachment; filename="file.txt"',
            $response->getHeaderValue('Content-Disposition')
        );
    }

    public function testFromBinary(): void
    {
        $data = file_get_contents('tests/Mock/test.txt');

        $response = DownloadResponse::fromBinary($data, [
            'filename' => 'file.txt',
        ]);

        $this->assertInstanceOf(
            DownloadResponse::class,
            $response
        );

        $this->assertSame(
            'text/plain; charset=UTF-8',
            $response->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            'attachment; filename="file.txt"',
            $response->getHeaderValue('Content-Disposition')
        );

        $this->assertSame(
            '15',
            $response->getHeaderValue('Content-Length')
        );

        $this->assertSame(
            'This is a test.',
            $response->getFile()->contents()
        );
    }

    public function testHeaders(): void
    {
        $response = new DownloadResponse('tests/Mock/test.txt');

        $this->assertSame(
            'text/plain; charset=UTF-8',
            $response->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            'attachment; filename="test.txt"',
            $response->getHeaderValue('Content-Disposition')
        );

        $this->assertSame(
            '0',
            $response->getHeaderValue('Expires')
        );

        $this->assertSame(
            'binary',
            $response->getHeaderValue('Content-Transfer-Encoding')
        );

        $this->assertSame(
            '15',
            $response->getHeaderValue('Content-Length')
        );

        $this->assertSame(
            'private, no-transform, no-store, must-revalidate',
            $response->getHeaderValue('Cache-Control')
        );
    }

    public function testInvalidFile(): void
    {
        $this->expectException(ServerException::class);

        $response = new DownloadResponse('tests/Mock/invalid.txt');
    }

    public function testMimeType(): void
    {
        $response = new DownloadResponse('tests/Mock/test.txt', [
            'mimeType' => 'application/octet-stream',
        ]);

        $this->assertSame(
            'application/octet-stream; charset=UTF-8',
            $response->getHeaderValue('Content-Type')
        );
    }

    public function testResponse(): void
    {
        $response = new DownloadResponse('tests/Mock/test.txt');

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testSetBody(): void
    {
        $this->expectException(ServerException::class);

        $response = new DownloadResponse('tests/Mock/test.txt');
        $response->setBody('test');
    }
}
