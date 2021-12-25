<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Locale;

trait LocaleTest
{

    public function testGetDefaultLocale(): void
    {
        $this->assertEquals(
            'en',
            $this->request->getDefaultLocale()
        );
    }

    public function testGetLocale(): void
    {
        $this->assertEquals(
            'en-us',
            $this->request->getLocale()
        );
    }

    public function testSetLocale(): void
    {
        $this->assertEquals(
            $this->request,
            $this->request->setLocale('en')
        );

        $this->assertEquals(
            'en',
            $this->request->getLocale()
        );
    }

    public function testSetLocaleInvalid(): void
    {
        $this->request->setLocale('ru');

        $this->assertEquals(
            'en',
            $this->request->getLocale()
        );
    }

    public function testSetLocaleLocale(): void
    {
        $this->request->setLocale('en');
        $this->request->setLocale('en-us');

        $this->assertEquals(
            'en-us',
            Locale::getDefault()
        );
    }

}
