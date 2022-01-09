<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use
    Fyre\DateTime\DateTime;

trait DateTest
{

    public function testSetDate(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->setDate(0)
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Date')
        );
    }

    public function testSetDateDateTime(): void
    {
        $date = DateTime::fromTimestamp(0);

        $this->response->setDate($date);

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Date')
        );
    }

    public function testSetDateNativeDateTime(): void
    {
        $date = new \DateTime('@0');

        $this->response->setDate($date);

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Date')
        );
    }

    public function testSetLastModified(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->setLastModified(0)
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Last-Modified')
        );
    }

    public function testSetLastModifiedDateTime(): void
    {
        $date = DateTime::fromTimestamp(0);

        $this->response->setLastModified($date);

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Last-Modified')
        );
    }

    public function testSetLastModifiedNativeDateTime(): void
    {
        $date = new \DateTime('@0');

        $this->response->setLastModified($date);

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $this->response->getHeaderValue('Last-Modified')
        );
    }

}
