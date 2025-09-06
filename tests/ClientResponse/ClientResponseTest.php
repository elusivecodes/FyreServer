<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use Fyre\Server\ClientResponse;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

use function class_uses;
use function json_decode;

final class ClientResponseTest extends TestCase
{
    use ContentTypeTestTrait;
    use CookieTestTrait;
    use DateTestTrait;

    protected ClientResponse $response;

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(ClientResponse::class)
        );
    }

    public function testNoCache(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->noCache();

        $this->assertNull(
            $response1->getHeaderValue('Cache-Control')
        );

        $this->assertSame(
            'no-store, max-age=0, no-cache',
            $response2->getHeaderValue('Cache-Control')
        );
    }

    public function testSetJson(): void
    {
        $response1 = new ClientResponse();
        $response2 = $response1->setJson(['a' => 1]);

        $this->assertSame(
            '',
            $response1->getBody()
        );

        $this->assertSame(
            'text/html; charset=UTF-8',
            $response1->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            [
                'a' => 1,
            ],
            json_decode($response2->getBody(), true)
        );

        $this->assertSame(
            'application/json; charset=UTF-8',
            $response2->getHeaderValue('Content-Type')
        );
    }

    public function testSetXml(): void
    {
        $xml = new SimpleXMLElement('<books><book><title>Test</title></book></books>');

        $response1 = new ClientResponse();
        $response2 = $response1->setXml($xml);

        $this->assertSame(
            '',
            $response1->getBody()
        );

        $this->assertSame(
            'text/html; charset=UTF-8',
            $response1->getHeaderValue('Content-Type')
        );

        $this->assertSame(
            $xml->asXML(),
            $response2->getBody()
        );

        $this->assertSame(
            'application/xml; charset=UTF-8',
            $response2->getHeaderValue('Content-Type')
        );
    }
}
