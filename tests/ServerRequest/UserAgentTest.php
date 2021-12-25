<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use
    Fyre\Http\UserAgent;

trait UserAgentTest
{

    public function testUserAgent(): void
    {
        $this->assertInstanceOf(
            UserAgent::class,
            $this->request->getUserAgent()
        );
    }

    public function testServerUserAgent(): void
    {
        $this->request->setGlobals('server', [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 ( .NET CLR 3.5.30729)'
        ]);
    
        $this->assertEquals(
            'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 ( .NET CLR 3.5.30729)',
            $this->request->getUserAgent()->getAgentString()
        );
    }

}
