<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Fyre\Uri\Uri;

trait UriTest
{

    public function testUri(): void
    {
        $this->assertInstanceOf(
            Uri::class,
            $this->request->getUri()
        );
    }

    public function testServerUri(): void
    {
        $this->assertEquals(
            'https://test.com/',
            $this->request->getUri()->getUri()
        );
    }

    public function testServerUriPath(): void
    {
        $this->request->setGlobals('server', [
            'REQUEST_URI' => '/test/path'
        ]);
    
        $this->assertEquals(
            '/test/path',
            $this->request->getUri()->getPath()
        );
    }

    public function testServerUriQuery(): void
    {
        $this->request->setGlobals('server', [
            'QUERY_STRING' => '?a=1&b=2'
        ]);
    
        $this->assertEquals(
            [
                'a' => 1,
                'b' => 2
            ],
            $this->request->getUri()->getQuery()
        );
    }

}
