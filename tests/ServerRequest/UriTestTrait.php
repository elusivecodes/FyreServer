<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Http\Uri;
use Fyre\Server\ServerRequest;

trait UriTestTrait
{

    public function testUri(): void
    {
        $request = new ServerRequest();

        $this->assertInstanceOf(
            Uri::class,
            $request->getUri()
        );
    }

    public function testServerUri(): void
    {
        $request = new ServerRequest([
            'baseUri' => 'https://test.com/'
        ]);

        $this->assertSame(
            'https://test.com/',
            $request->getUri()->getUri()
        );
    }

    public function testServerUriPath(): void
    {
        $request = new ServerRequest([
            'baseUri' => 'https://test.com/',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/path'
                ]
            ]
        ]);

        $this->assertSame(
            '/test/path',
            $request->getUri()->getPath()
        );
    }

    public function testServerUriQuery(): void
    {
        $request = new ServerRequest([
            'baseUri' => 'https://test.com/',
            'globals' => [
                'server' => [
                    'QUERY_STRING' => '?a=1&b=2'
                ]
            ]
        ]);

        $this->assertSame(
            [
                'a' => '1',
                'b' => '2'
            ],
            $request->getUri()->getQuery()
        );
    }

}
