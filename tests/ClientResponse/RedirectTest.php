<?php
declare(strict_types=1);

namespace Tests\ClientResponse;

trait RedirectTest
{

    public function testRedirect(): void
    {
        $this->assertEquals(
            $this->response,
            $this->response->redirect('https://test.com/')
        );

        $this->assertEquals(
            'https://test.com/',
            $this->response->getHeaderValue('Location')
        );

        $this->assertEquals(
            302,
            $this->response->getStatusCode()
        );
    }

    public function testRedirectWithCode(): void
    {
        $this->response->redirect('https://test.com/', 301);

        $this->assertEquals(
            301,
            $this->response->getStatusCode()
        );
    }

}
