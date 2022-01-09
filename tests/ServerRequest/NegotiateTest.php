<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Fyre\Server\Exceptions\ServerException;

trait NegotiateTest
{

    public function testNegotiateMedia(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,/;q=0.8'
        ]);

        $this->assertSame(
            'text/html',
            $this->request->negotiate('content', ['application/xml', 'text/html'])
        );
    }

    public function testNegotiateEncoding(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate'
        ]);

        $this->assertSame(
            'gzip',
            $this->request->negotiate('encoding', ['deflate', 'gzip'])
        );
    }

    public function testNegotiateLanguage(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5'
        ]);

        $this->assertSame(
            'en-gb',
            $this->request->negotiate('language', ['en-gb'])
        );
    }

    public function testNegotiateInvalid(): void
    {
        $this->expectException(ServerException::class);

        $this->request->negotiate('invalid', []);
    }

}
