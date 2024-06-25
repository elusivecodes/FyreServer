<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\Exceptions\ServerException;
use Fyre\Server\ServerRequest;

trait LocaleTestTrait
{
    public function testGetDefaultLocale(): void
    {
        $request = new ServerRequest();

        $this->assertSame(
            'en',
            $request->getDefaultLocale()
        );
    }

    public function testGetLocale(): void
    {
        $request = new ServerRequest();

        $this->assertSame(
            'en',
            $request->getLocale()
        );
    }

    public function testSetLocale(): void
    {
        $request1 = new ServerRequest([
            'supportedLocales' => ['en-US'],
        ]);
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

        $request = new ServerRequest();

        $request->setLocale('ru');
    }
}
