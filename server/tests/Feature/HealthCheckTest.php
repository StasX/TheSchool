<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_example(): void
    {
        $response = $this->get('/api/healthcheck');

        $response->assertOk();
    }
}
