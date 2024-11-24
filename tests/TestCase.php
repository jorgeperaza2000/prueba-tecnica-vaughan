<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    public function makePostRequestV1(string $url, array $headers, array $data): TestResponse
    {
        $response = $this->withHeaders($headers)->postJson('/api/v1/' . $url, $data);

        return $response;
    }
}
