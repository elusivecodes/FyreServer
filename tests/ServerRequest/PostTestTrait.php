<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\DateTime\DateTime;
use Fyre\Server\ServerRequest;

trait PostTestTrait
{
    public function testGetPost(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            'value',
            $request->getPost('test')
        );
    }

    public function testGetPostAll(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => 'value',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'test' => 'value',
            ],
            $request->getPost()
        );
    }

    public function testGetPostArray(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value',
                    ],
                ],
            ],
        ]);

        $this->assertSame(
            [
                'a' => 'value',
            ],
            $request->getPost('test')
        );
    }

    public function testGetPostDot(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value',
                    ],
                ],
            ],
        ]);

        $this->assertSame(
            'value',
            $request->getPost('test.a')
        );
    }

    public function testGetPostInvalid(): void
    {
        $request = new ServerRequest($this->config, $this->type);

        $this->assertNull(
            $request->getPost('invalid')
        );
    }

    public function testGetPostType(): void
    {
        $request = new ServerRequest($this->config, $this->type, [
            'globals' => [
                'post' => [
                    'test' => '2024-12-31',
                ],
            ],
        ]);

        $value = $request->getPost('test', 'date');

        $this->assertInstanceOf(
            DateTime::class,
            $value
        );

        $this->assertSame(
            '2024-12-31T00:00:00.000+00:00',
            $value->toISOString()
        );
    }
}
