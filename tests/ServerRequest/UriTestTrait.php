<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Http\Uri;
use Fyre\Server\ServerRequest;

trait UriTestTrait
{
    public function testServerUri(): void
    {
        $this->config->set('App.baseUri', 'https://test.com/');

        $request = new ServerRequest($this->config, $this->type);

        $this->assertSame(
            'https://test.com/',
            $request->getUri()->getUri()
        );
    }

    public function testServerUriPath(): void
    {
        $this->config->set('App.baseUri', 'https://test.com/');

        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/path',
                ],
            ],
        ]);

        $this->assertSame(
            '/test/path',
            $request->getUri()->getPath()
        );
    }

    public function testServerUriQuery(): void
    {
        $this->config->set('App.baseUri', 'https://test.com/');

        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'server' => [
                    'QUERY_STRING' => '?a=1&b=2',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'a' => '1',
                'b' => '2',
            ],
            $request->getUri()->getQuery()
        );
    }

    public function testUri(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertInstanceOf(
            Uri::class,
            $request->getUri()
        );
    }
}
