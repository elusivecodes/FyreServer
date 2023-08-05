<?php
declare(strict_types=1);

namespace Tests\ServerRequest;

use Fyre\Server\ServerRequest;

use const FILTER_VALIDATE_EMAIL;

trait PostTestTrait
{

    public function testGetPost(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            'value',
            $request->getPost('test')
        );
    }

    public function testGetPostDot(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value'
                    ]
                ]
            ]
        ]);

        $this->assertSame(
            'value',
            $request->getPost('test.a')
        );
    }

    public function testGetPostArray(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => [
                        'a' => 'value'
                    ]
                ]
            ]
        ]);

        $this->assertSame(
            [
                'a' => 'value'
            ],
            $request->getPost('test')
        );
    }

    public function testGetPostFilter(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            '',
            $request->getPost('test', FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetPostAll(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'post' => [
                    'test' => 'value'
                ]
            ]
        ]);

        $this->assertSame(
            [
                'test' => 'value'
            ],
            $request->getPost()
        );
    }

    public function testGetPostInvalid(): void
    {
        $request = new ServerRequest();

        $this->assertNull(
            $request->getPost('invalid')
        );
    }

}
