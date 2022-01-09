<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Locale;

trait LocaleTest
{

    public function testGetDefaultLocale(): void
    {
        $this->assertSame(
            'en',
            $this->request->getDefaultLocale()
        );
    }

    public function testGetLocale(): void
    {
        $this->assertSame(
            'en-us',
            $this->request->getLocale()
        );
    }

    public function testSetLocale(): void
    {
        $this->assertSame(
            $this->request,
            $this->request->setLocale('en')
        );

        $this->assertSame(
            'en',
            $this->request->getLocale()
        );
    }

    public function testSetLocaleInvalid(): void
    {
        $this->request->setLocale('ru');

        $this->assertSame(
            'en',
            $this->request->getLocale()
        );
    }

    public function testSetLocaleLocale(): void
    {
        $this->request->setLocale('en');
        $this->request->setLocale('en-us');

        $this->assertSame(
            'en-us',
            Locale::getDefault()
        );
    }

}
