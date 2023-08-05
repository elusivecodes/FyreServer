<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use Fyre\DateTime\DateTime;
use Fyre\Server\ClientResponse;

trait DateTestTrait
{

    public function testSetDate(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setDate('0');

        $this->assertNull(
            $response1->getHeaderValue('Date')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Date')
        );
    }

    public function testSetDateDateTime(): void
    {
        $date = DateTime::fromTimestamp(0);

        $response1 = new ClientResponse();
        $response2 = $response1->setDate($date);

        $this->assertNull(
            $response1->getHeaderValue('Date')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Date')
        );
    }

    public function testSetDateNativeDateTime(): void
    {
        $date = new \DateTime('@0');

        $response1 = new ClientResponse();
        $response2 = $response1->setDate($date);

        $this->assertNull(
            $response1->getHeaderValue('Date')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Date')
        );
    }

    public function testSetLastModified(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setLastModified('0');

        $this->assertNull(
            $response1->getHeaderValue('Last-Modified')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Last-Modified')
        );
    }

    public function testSetLastModifiedDateTime(): void
    {
        $date = DateTime::fromTimestamp(0);

        $response1 = new ClientResponse();
        $response2 = $response1->setLastModified($date);

        $this->assertNull(
            $response1->getHeaderValue('Last-Modified')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Last-Modified')
        );
    }

    public function testSetLastModifiedNativeDateTime(): void
    {
        $date = new \DateTime('@0');

        $response1 = new ClientResponse();
        $response2 = $response1->setLastModified($date);

        $this->assertNull(
            $response1->getHeaderValue('Last-Modified')
        );

        $this->assertSame(
            'Thu, 01-Jan-1970 00:00:00 UTC',
            $response2->getHeaderValue('Last-Modified')
        );
    }

}
