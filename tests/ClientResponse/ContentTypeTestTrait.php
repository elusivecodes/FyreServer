<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use Fyre\Server\ClientResponse;

trait ContentTypeTestTrait
{
    public function testContentType(): void
    {
        $response = new ClientResponse();

        $this->assertSame(
            'text/html; charset=UTF-8',
            $response->getHeaderValue('Content-Type')
        );
    }

    public function testSetContentType(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setContentType('image/jpeg');

        $this->assertSame(
            'text/html; charset=UTF-8',
            $response1->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            'image/jpeg; charset=UTF-8',
            $response2->getHeaderValue('Content-Type')
        );
    }

    public function testSetContentTypeWithCharset(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setContentType('image/jpeg', 'UTF-16');

        $this->assertSame(
            'text/html; charset=UTF-8',
            $response1->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            'image/jpeg; charset=UTF-16',
            $response2->getHeaderValue('Content-Type')
        );
    }
}
