<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use function putenv;

use const FILTER_VALIDATE_EMAIL;

trait EnvTestTrait
{
    public function testGetEnv(): void
    {
        putenv('test=value');

        $request = new ServerRequest($this->config);

        $this->assertSame(
            'value',
            $request->getEnv('test')
        );
    }

    public function testGetEnvFilter(): void
    {
        putenv('value=test');

        $request = new ServerRequest($this->config);

        $this->assertSame(
            '',
            $request->getEnv('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetEnvInvalid(): void
    {
        $request = new ServerRequest($this->config);

        $this->assertNull(
            $request->getEnv('invalid')
        );
    }
}
