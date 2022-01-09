<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use const
    FILTER_VALIDATE_EMAIL;

use function
    putenv;

trait EnvTest
{

    public function testGetEnv(): void
    {
        putenv('test=value');

        $this->assertSame(
            'value',
            $this->request->getEnv('test')
        );
    }

    public function testGetEnvFilter(): void
    {
        putenv('value=test');

        $this->assertSame(
            '',
            $this->request->getEnv('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetEnvInvalid(): void
    {
        $this->assertNull(
            $this->request->getEnv('invalid')
        );
    }

}
