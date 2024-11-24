<?php

namespace Tests\Feature\Api\v1;

use Tests\TestCase;

class shortUrlsTest extends TestCase
{
    public function testHeaderAuthorizationIsMissing(): void
    {
        $response = $this->withHeaders([])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['Authorization']);
    }

    public function testHeaderAuthorizationIsInvalid(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer {invalid]-token}',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['Authorization']);
    }

    public function testHeaderAuthorizationDoesNotIncludeBearerWordAtStart(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'toket-without-bearer',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['Authorization']);
    }

    public function testHeaderAuthorizationBlankIsValid(): void
    {
        $response = $this->withHeaders([
            'Authorization' => '',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(200);
    }

    public function testHeaderAuthorizationIsValid_one(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer {}[]()',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(200);
    }

    public function testHeaderAuthorizationIsValid_two(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer {}',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(200);
    }

    public function testHeaderAuthorizationIsValid_three(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer {([])}',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(200);
    }

    public function testHeaderAuthorizationInvalid_one(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer {)',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(401);
        $response->assertJsonValidationErrors(['Authorization']);
    }

    public function testUrlIsRequired(): void
    {
        $response = $this->withHeaders([
            'Authorization' => '',
        ])->postJson($this->API_V1_PATH . '/short-urls', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testUrlMustBeValid(): void
    {
        $response = $this->withHeaders([
            'Authorization' => '',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'invalid-url',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testUrlIsValid(): void
    {
        $response = $this->withHeaders([
            'Authorization' => '',
        ])->postJson($this->API_V1_PATH . '/short-urls', [
            'url' => 'http://example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);
    }
}
