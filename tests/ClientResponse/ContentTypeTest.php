<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

trait ContentTypeTest
{

    public function testContentType(): void
    {
        $this->assertSame(
            'text/html; charset=UTF-8',
            $this->response->getHeaderValue('Content-Type')
        );
    }

    public function testSetContentType(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->setContentType('image/jpeg')
        );

        $this->assertSame(
            'image/jpeg; charset=UTF-8',
            $this->response->getHeaderValue('Content-Type')
        );
    }

    public function testSetContentTypeWithCharset(): void
    {
        $this->response->setContentType('image/jpeg', 'UTF-16');

        $this->assertSame(
            'image/jpeg; charset=UTF-16',
            $this->response->getHeaderValue('Content-Type')
        );
    }

}
