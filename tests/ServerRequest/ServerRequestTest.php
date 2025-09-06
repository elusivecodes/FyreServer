<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\DB\TypeParser;
use Fyre\Http\Request;
use Fyre\Server\ServerRequest;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

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

    protected TypeParser $type;

    public function testIsAjax(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertFalse(
            $request->isAjax()
        );
    }

    public function testIsAjaxTrue(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type);

        $this->assertTrue(
            $request->isCli()
        );
    }

    public function testIsSecure(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertFalse(
            $request->isSecure()
        );
    }

    public function testIsSecureForwardedProto(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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
        $request = new ServerRequest($this->config, $this->type, [
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

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(ServerRequest::class)
        );
    }

    public function testRequest(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertInstanceOf(
            Request::class,
            $request
        );
    }

    public function testSetGlobal(): void
    {
        $request1 = new ServerRequest($this->config, $this->type);
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
        $request1 = new ServerRequest($this->config, $this->type);
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
        $this->config->set('App.defaultLocale', 'en');

        $this->type = Container::getInstance()->use(TypeParser::class);
    }
}
