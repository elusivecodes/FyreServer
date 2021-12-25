<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

use
    Fyre\Cookie\CookieStore,
    Fyre\Http\Response,
    Fyre\Server\ClientResponse,
    PHPUnit\Framework\TestCase,
    SimpleXMLElement;

use function
    json_decode;

final class ClientResponseTest extends TestCase
{

    protected ClientResponse $response;

    use
        ContentTypeTest,
        CookieTest,
        DateTest,
        RedirectTest;

    public function testResponse(): void
    {
        $this->assertInstanceOf(
            Response::class,
            $this->response
        );
    }

    public function testNoCache(): void
    {
        $this->assertEquals(
            'no-store, max-age=0, no-cache',
            $this->response->getHeaderValue('Cache-Control')
        );
    }

    public function testSetJson(): void
    {
        $this->assertEquals(
            $this->response,
            $this->response->setJson(['a' => 1])
        );

        $json = $this->response->getBody();

        $this->assertEquals(
            [
                'a' => 1
            ],
            json_decode($json, true)
        );

        $this->assertEquals(
            'application/json; charset=UTF-8',
            $this->response->getHeaderValue('Content-Type')
        );
    }

    public function testSetXml(): void
    {
        $xml = new SimpleXMLElement('<books><book><title>Test</title></book></books>');

        $this->assertEquals(
            $this->response,
            $this->response->setXml($xml)
        );

        $this->assertEquals(
            $xml->asXML(),
            $this->response->getBody()
        );

        $this->assertEquals(
            'application/xml; charset=UTF-8',
            $this->response->getHeaderValue('Content-Type')
        );
    }

    public function setUp(): void
    {
        CookieStore::clear();

        $this->response = new ClientResponse();
    }

}
