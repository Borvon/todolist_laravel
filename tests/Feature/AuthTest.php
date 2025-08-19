<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $http;

    public function setUp(): void
    {   
        parent::setUp();

        $this->http = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false
        ]);
    }

    public function testRegisterWithValidData()
    {
        $credentials = [
            'login' => 'testUser',
            'password' => 'testPassword123'
        ];

        $response = $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertEquals($credentials['login'], $body['login']);
    }

    public function testRegisterWithExistingLogin(): void
    {
        $credentials = [
            'login' => 'userWithThatLogin',
            'password' => 'testPassword123'
        ];

        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);

        $credentials = [
            'login' => 'userWithThatLogin',
            'password' => 'passwordForSecondUserWithThatLogin'
        ];
        $response = $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);
        $this->assertEquals(422, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Integrity constraint violation', $body['message']);
    }

    public function testRegisterValidationFail(): void
    {
        $credentials = [
            'login' => '',
            'password' => 'testPassword123'
        ];

        $response = $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Validation failed', $body['message']);

        $credentials = [
            'login' => 'testUser',
            'password' => ''
        ];

        $response = $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Validation failed', $body['message']);
    }

    public function testLoginWithValidCredentials()
    {
        $credentials = [
            'login' => 'userForLoginTest',
            'password' => 'testPassword321'
        ];
        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $this->assertEquals(200, $response->getStatusCode());
        
        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('access_token', $body);
        $this->assertArrayHasKey('token_type', $body);
    }

    public function testLoginWithInvalidCredentials()
    {
        $credentials = [
            'login' => 'userForLoginTestWithInvalidCredentials',
            'password' => 'testPassword213'
        ];
        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);

        $credentials['password'] = 'wrongPassword';

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $this->assertEquals(401, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Not authenticated', $body['message']);
    }

    public function testLoginValidationFail()
    {
        $credentials = [
            'login' => '',
            'password' => 'testPassword123'
        ];

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Validation failed', $body['message']);

        $credentials = [
            'login' => 'testUser',
            'password' => ''
        ];

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertStringContainsString('Validation failed', $body['message']);
    }

    public function testMeWhileAuthenticated()
    {
        $credentials = [
            'login' => 'userForTestMe',
            'password' => 'testPassword543'
        ];

        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);
        $loginResponse = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $loginResponseBody = json_decode($loginResponse->getBody(), true);

        $response = $this->http->request('GET', '/api/v1/me', ['headers' => ['Authorization' => 'Bearer '.$loginResponseBody['access_token']]]);
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertEquals($credentials['login'], $body['login']);
    }

    public function testMeWhileNotAuthenticated()
    {
        $response = $this->http->request('GET', '/api/v1/me');
        $this->assertEquals(401, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('message', $body);
        $this->assertEquals('Not authenticated', $body['message']);
    }
}