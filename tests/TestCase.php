<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function assertAuthenticatedOnly(string $route, string $method = 'post'): void
    {
        $response = $this->{$method."Json"}($route);
        $response->assertUnauthorized();
    }

    public function assertAdminsOnly(string $route, string $method = 'post'): void
    {
        $this->assertAuthenticatedOnly($route, $method);
    }
}
