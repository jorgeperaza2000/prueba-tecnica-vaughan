<?php

namespace Tests\Feature\Api\v1;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class shortUrlsTest extends TestCase
{
    const ENDPOINT = 'short-urls';
    const HEADER_BLANK = [];
    const HEADER_WITHOUT_BEARER = ['Authorization' => 'token-without-bearer'];
    const HEADER_BLANK_TOKEN = ['Authorization' => 'Bearer '];
    const HEADER_VALID_TOKEN = [
        ['Authorization' => 'Bearer '],
        ['Authorization' => 'Bearer {}'],
        ['Authorization' => 'Bearer {}[]()'],
        ['Authorization' => 'Bearer {([])}'],
    ];
    const HEADER_INVALID_TOKEN = [
        ['Authorization' => 'Bearer {invalid]-token}'],
        ['Authorization' => 'Bearer {)'],
        ['Authorization' => 'Bearer [{]}'],
        ['Authorization' => 'Bearer (((((((()'],
    ];

    public function testHeaderAuthorizationIsMissing(): void
    {
        $data = ['url' => 'http://example.com'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK,
            $data
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['Authorization' => 'The Authorization header is missing.']);
    }

    public function testHeaderAuthorizationDoesNotIncludeBearerWordAtStart(): void
    {
        $data = ['url' => 'http://example.com'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_WITHOUT_BEARER,
            $data
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['Authorization' => 'The Authorization header is invalid.']);
    }

    public function testHeaderAuthorizationBlankIsValid(): void
    {
        $data = ['url' => 'http://example.com'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK_TOKEN,
            $data
        );

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);
    }

    public function testHeaderAuthorizationIsValid(): void
    {
        $data = ['url' => 'http://example.com'];

        foreach ($this::HEADER_VALID_TOKEN as $validToken) {
            $response = $this->makePostRequestV1(
                $this::ENDPOINT,
                $validToken,
                $data
            );

            $response->assertStatus(200);
            $response->assertJsonStructure(['url']);
        }
    }

    public function testHeaderAuthorizationIsInvalid(): void
    {
        $data = ['url' => 'http://example.com'];

        foreach ($this::HEADER_INVALID_TOKEN as $invalidToken) {
            $response = $this->makePostRequestV1(
                $this::ENDPOINT,
                $invalidToken,
                $data
            );

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['Authorization' => 'The Authorization token is invalid.']);
        }
    }

    public function testUrlIsRequired(): void
    {
        $data = [];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK_TOKEN,
            $data
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url' => 'The url field is required.']);
    }

    public function testUrlMustBeValid(): void
    {
        $data = ['url' => 'invalid-url'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK_TOKEN,
            $data
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url' => 'The url field must be a valid URL.']);
    }

    public function testUrlIsValid(): void
    {
        Http::fake([
            'https://tinyurl.com/api-create.php*' => Http::response('https://tinyurl.com/abc123', 200),
        ]);

        $data = ['url' => 'http://example.com'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK_TOKEN,
            $data
        );

        $response->assertStatus(200);
        $response->assertJson(['url' => 'https://tinyurl.com/abc123']);
    }

    public function testUrlIsValidButResponseWithError(): void
    {
        Http::fake([
            'https://tinyurl.com/api-create.php*' => Http::response('error', 500),
        ]);

        $data = ['url' => 'http://example.com'];

        $response = $this->makePostRequestV1(
            $this::ENDPOINT,
            $this::HEADER_BLANK_TOKEN,
            $data
        );

        $response->assertStatus(500);
        $response->assertJsonStructure(['error']);
    }
}
