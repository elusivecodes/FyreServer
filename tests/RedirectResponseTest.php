<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use Fyre\Server\ClientResponse;
use Fyre\Server\Exceptions\ServerException;
use Fyre\Server\RedirectResponse;
use PHPUnit\Framework\TestCase;

final class RedirectResponseTest extends TestCase
{
    public function testRedirect(): void
    {
        $response = new RedirectResponse('https://test.com/');

        $this->assertSame(
            'https://test.com/',
            $response->getHeaderValue('Location')
        );

        $this->assertSame(
            302,
            $response->getStatusCode()
        );
    }

    public function testRedirectWithCode(): void
    {
        $response = new RedirectResponse('https://test.com/', 301);

        $this->assertSame(
            'https://test.com/',
            $response->getHeaderValue('Location')
        );

        $this->assertSame(
            301,
            $response->getStatusCode()
        );
    }

    public function testResponse(): void
    {
        $response = new RedirectResponse('https://test.com/');

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );
    }

    public function testSetBody(): void
    {
        $this->expectException(ServerException::class);

        $response = new RedirectResponse('https://test.com/');
        $response->setBody('test');
    }
}
