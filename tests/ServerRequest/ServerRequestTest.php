<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Fyre\Http\Request,
    Fyre\Server\ServerRequest,
    Locale,
    PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{

    protected ServerRequest $request;

    use
        CookieTest,
        EnvTest,
        FileTest,
        GetTest,
        LocaleTest,
        NegotiateTest,
        PostTest,
        ServerTest,
        UriTest,
        UserAgentTest;

    public function testRequest(): void
    {
        $this->assertInstanceOf(
            Request::class,
            $this->request
        );
    }

    public function testIsAjax(): void
    {
        $this->assertFalse(
            $this->request->isAjax()
        );
    }

    public function testIsAjaxTrue(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_X_REQUESTED_WITH' => 'XmlHttpRequest'
        ]);

        $this->assertTrue(
            $this->request->isAjax()
        );
    }

    public function testIsCli(): void
    {
        $this->assertTrue(
            $this->request->isCli()
        );
    }

    public function testIsSecure(): void
    {
        $this->assertFalse(
            $this->request->isSecure()
        );
    }

    public function testIsSecureHttps(): void
    {
        $this->request->setGlobals('server', [
            'HTTPS' => 'ON'
        ]);

        $this->assertTrue(
            $this->request->isSecure()
        );
    }

    public function testIsSecureForwardedProto(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_X_FORWARDED_PROTO' => 'https'
        ]);

        $this->assertTrue(
            $this->request->isSecure()
        );
    }

    public function testIsSecureFrontEndHttps(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_FRONT_END_HTTPS' => 'ON'
        ]);

        $this->assertTrue(
            $this->request->isSecure()
        );
    }

    protected function setUp(): void
    {
        Locale::setDefault('en');

        $this->request = new ServerRequest([
            'baseUri' => 'https://test.com/',
            'supportedLocales' => ['en-us', 'en']
        ]);
    }

}
