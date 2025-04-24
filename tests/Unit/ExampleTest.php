<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_environment_is_testing()
    {
        $this->assertEquals('testing', env('APP_ENV'));
    }
}
