<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

trait RedirectTest
{

    public function testRedirect(): void
    {
        $this->assertSame(
            $this->response,
            $this->response->redirect('https://test.com/')
        );

        $this->assertSame(
            'https://test.com/',
            $this->response->getHeaderValue('Location')
        );

        $this->assertSame(
            302,
            $this->response->getStatusCode()
        );
    }

    public function testRedirectWithCode(): void
    {
        $this->response->redirect('https://test.com/', 301);

        $this->assertSame(
            301,
            $this->response->getStatusCode()
        );
    }

}
