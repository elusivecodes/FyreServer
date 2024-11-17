<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\Exceptions\ServerException;
use Fyre\Server\ServerRequest;

trait LocaleTestTrait
{
    public function testGetDefaultLocale(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertSame(
            'en',
            $request->getDefaultLocale()
        );
    }

    public function testGetLocale(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertSame(
            'en',
            $request->getLocale()
        );
    }

    public function testSetLocale(): void
    {
        $this->config->set('App.supportedLocales', ['en-US']);

        $request1 = new ServerRequest($this->config);
        $request2 = $request1->setLocale('en-US');

        $this->assertSame(
            'en',
            $request1->getLocale()
        );

        $this->assertSame(
            'en-US',
            $request2->getLocale()
        );
    }

    public function testSetLocaleInvalid(): void
    {
        $this->expectException(ServerException::class);

        $request = new ServerRequest($this->config);

        $request->setLocale('ru');
    }
}
