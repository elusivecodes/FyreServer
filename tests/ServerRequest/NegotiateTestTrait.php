<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\Exceptions\ServerException;
use Fyre\Server\ServerRequest;

trait NegotiateTestTrait
{
    public function testNegotiateEncoding(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
                ],
            ],
        ]);

        $this->assertSame(
            'gzip',
            $request->negotiate('encoding', ['deflate', 'gzip'])
        );
    }

    public function testNegotiateInvalid(): void
    {
        $this->expectException(ServerException::class);

        $request = new ServerRequest();

        $request->negotiate('invalid', []);
    }

    public function testNegotiateLanguage(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5',
                ],
            ],
        ]);

        $this->assertSame(
            'en-gb',
            $request->negotiate('language', ['en-gb'])
        );
    }

    public function testNegotiateMedia(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8',
                ],
            ],
        ]);

        $this->assertSame(
            'text/html',
            $request->negotiate('content', ['application/xml', 'text/html'])
        );
    }
}
