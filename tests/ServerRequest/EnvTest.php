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

        $this->assertEquals(
            'value',
            $this->request->getEnv('test')
        );
    }

    public function testGetEnvFilter(): void
    {
        putenv('value=test');

        $this->assertEquals(
            '',
            $this->request->getEnv('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetEnvInvalid(): void
    {
        $this->assertEquals(
            null,
            $this->request->getEnv('invalid')
        );
    }

}
