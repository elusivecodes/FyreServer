<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Config\Config;
use Fyre\Http\Request;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{
    use CookieTestTrait;
    use DataTestTrait;
    use EnvTestTrait;
    use FileTestTrait;
    use JsonTestTrait;
    use LocaleTestTrait;
    use NegotiateTestTrait;
    use PostTestTrait;
    use QueryTestTrait;
    use ServerTestTrait;
    use UriTestTrait;
    use UserAgentTestTrait;

    protected Config $config;

    public function testIsAjax(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertFalse(
            $request->isAjax()
        );
    }

    public function testIsAjaxTrue(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'server' => [
                    'HTTP_X_REQUESTED_WITH' => 'XmlHttpRequest',
                ],
            ],
        ]);

        $this->assertTrue(
            $request->isAjax()
        );
    }

    public function testIsCli(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertTrue(
            $request->isCli()
        );
    }

    public function testIsSecure(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertFalse(
            $request->isSecure()
        );
    }

    public function testIsSecureForwardedProto(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'server' => [
                    'HTTP_X_FORWARDED_PROTO' => 'https',
                ],
            ],
        ]);

        $this->assertTrue(
            $request->isSecure()
        );
    }

    public function testIsSecureFrontEndHttps(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'server' => [
                    'HTTP_FRONT_END_HTTPS' => 'ON',
                ],
            ],
        ]);

        $this->assertTrue(
            $request->isSecure()
        );
    }

    public function testIsSecureHttps(): void
    {
        $request = new ServerRequest($this->config, [
            'globals' => [
                'server' => [
                    'HTTPS' => 'ON',
                ],
            ],
        ]);

        $this->assertTrue(
            $request->isSecure()
        );
    }

    public function testRequest(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertInstanceOf(
            Request::class,
            $request
        );
    }

    public function testSetGlobal(): void
    {
        $request1 = new ServerRequest($this->config);
        $request2 = $request1->setGlobal('post', [
            'test' => 'value',
        ]);

        $this->assertNull(
            $request1->getPost('test')
        );

        $this->assertSame(
            'value',
            $request2->getPost('test')
        );
    }

    public function testSetParam(): void
    {
        $request1 = new ServerRequest($this->config);
        $request2 = $request1->setParam('test', 'value');

        $this->assertNull(
            $request1->getParam('test')
        );

        $this->assertSame(
            'value',
            $request2->getParam('test')
        );
    }

    protected function setUp(): void
    {
        $this->config = new Config();
        $this->config->set('App.locale', 'en');
    }
}
