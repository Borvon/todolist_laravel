<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class TaskTest extends TestCase
{
    use RefreshDatabase;

    private $http;
    private string $token;
    private int $taskOfAnotherUser;

    public function setUp(): void
    {   
        parent::setUp();

        $this->http = new Client([
            'base_uri' => 'http://localhost:8000',
            'http_errors' => false
        ]);

        $credentials = [
            'login' => 'user',
            'password' => 'testPassword321'
        ];
        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $body = json_decode($response->getBody(), true);
        $this->token = $body['access_token'];

        $credentials = [
            'login' => 'anotherUser',
            'password' => 'testPassword321'
        ];
        $this->http->request('POST', '/api/v1/register', ['json' => $credentials]);

        $response = $this->http->request('POST', '/api/v1/login', ['json' => $credentials]);
        $body = json_decode($response->getBody(), true);
        $anotherToken = $body['access_token'];

        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$anotherToken]]);
        $body = json_decode($response->getBody(), true);
        $this->taskOfAnotherUser = $body['id'];
    }

    public function testPostTaskWithValidData()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('user_id', $body);
        $this->assertArrayHasKey('created_at', $body);
        $this->assertArrayHasKey('updated_at', $body);
        $this->assertEquals($taskData['title'], $body['title']);
        $this->assertEquals($taskData['description'], $body['description']);
        $this->assertEquals($taskData['status'], $body['status']);
    }

    public function testPostTaskValidationError()
    {
        $taskData = [
            'title' => '',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Validation failed', $body['message']);

        $taskData = [
            'title' => 'task',
            'description' => 'description',
            'status' => 'in_progressssssssssssssss', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(422, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Validation failed', $body['message']);
    }

    public function testPostTaskWhileNotAuthenticated()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData]);
        $this->assertEquals(403, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
    }

    public function testPutTaskWithValidData()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];

        $taskNewData = [
            'title' => 'newTestTask',
            'description' => 'newDescription',
            'status' => 'done', 
        ];

        $response = $this->http->request('PUT', '/api/v1/tasks/'.$taskId, ['json' => $taskNewData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('user_id', $body);
        $this->assertArrayHasKey('created_at', $body);
        $this->assertArrayHasKey('updated_at', $body);
        $this->assertEquals($taskNewData['title'], $body['title']);
        $this->assertEquals($taskNewData['description'], $body['description']);
        $this->assertEquals($taskNewData['status'], $body['status']);
    }

    public function testPutTaskValidationFailed()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];

        $taskNewData = [
            'title' => 'title',
            'description' => 'newDescription',
            'status' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 
        ];

        $response = $this->http->request('PUT', '/api/v1/tasks/'.$taskId, ['json' => $taskNewData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);

        $this->assertEquals(422, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals('Validation failed', $body['message']);
    }

    public function testPutTaskWhileNotAuthenticated()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];

        $taskNewData = [
            'title' => 'title',
            'description' => 'newDescription',
            'status' => 'done', 
        ];

        $response = $this->http->request('PUT', '/api/v1/tasks/'.$taskId, ['json' => $taskNewData]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testPutTaskWithoutAccess()
    {
        $taskNewData = [
            'title' => 'title',
            'description' => 'newDescription',
            'status' => 'done', 
        ];

        $response = $this->http->request('PUT', '/api/v1/tasks/'.$this->taskOfAnotherUser, ['json' => $taskNewData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testPutNotExistingTask()
    {
        $taskNewData = [
            'title' => 'title',
            'description' => 'newDescription',
            'status' => 'done', 
        ];

        $response = $this->http->request('PUT', '/api/v1/tasks/A', ['json' => $taskNewData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteTask()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];
        $response = $this->http->request('DELETE', '/api/v1/tasks/'.$taskId, ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteTaskWhileNotAuthenticated()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];
        $response = $this->http->request('DELETE', '/api/v1/tasks/'.$taskId);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteTaskWithoutAccess()
    {
        $response = $this->http->request('DELETE', '/api/v1/tasks/'.$this->taskOfAnotherUser, ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testDeleteNotExistingTask()
    {
        $response = $this->http->request('DELETE', '/api/v1/tasks/A', ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testShowTask()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];
        $response = $this->http->request('GET', '/api/v1/tasks/'.$taskId, ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('id', $body);
        $this->assertArrayHasKey('user_id', $body);
        $this->assertArrayHasKey('created_at', $body);
        $this->assertArrayHasKey('updated_at', $body);
        $this->assertEquals($taskData['title'], $body['title']);
        $this->assertEquals($taskData['description'], $body['description']);
        $this->assertEquals($taskData['status'], $body['status']);
    }

    public function testShowTaskWhileNotAuthenticated()
    {
        $taskData = [
            'title' => 'testTask',
            'description' => 'description',
            'status' => 'in_progress', 
        ];

        $response = $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $taskId = json_decode($response->getBody(), true)['id'];
        $response = $this->http->request('GET', '/api/v1/tasks/'.$taskId);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testShowTaskWithoutAccess()
    {
        $response = $this->http->request('GET', '/api/v1/tasks/'.$this->taskOfAnotherUser, ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testShowNotExistingTask()
    {
        $response = $this->http->request('GET', '/api/v1/tasks/A', ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testIndexTasks()
    {
        for ($i=0; $i<5; $i++)
        {
            $taskData = [
                'title' => 'testTask'.$i,
                'description' => 'description',
                'status' => 'in_progress', 
            ];
    
            $this->http->request('POST', '/api/v1/tasks', ['json' => $taskData, 'headers' => ['Authorization' => 'Bearer '.$this->token]]);
        }

        $response = $this->http->request('GET', '/api/v1/tasks?limit=2&offset=2', ['headers' => ['Authorization' => 'Bearer '.$this->token]]);
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertEquals(2, count($body['tasks']));
    }

    public function testIndexTasksWhileNotAuthenticated()
    {
        $response = $this->http->request('GET', '/api/v1/tasks?limit=2&offset=2');
        $this->assertEquals(403, $response->getStatusCode());
    }
}