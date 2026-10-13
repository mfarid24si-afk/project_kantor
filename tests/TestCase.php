<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'array',
            'session.connection' => null,
            'database.default' => 'sqlite',
        ]);

        $this->withoutMiddleware(PreventRequestForgery::class);
    }
}
