<?php

namespace Tests\Unit\Http\Controllers\Api;

use App\Dto\SubmissionDto;
use App\Events\SubmissionSaved;
use App\Http\Controllers\Api\SubmissionsController;
use App\Http\Requests\SubmissionCreateRequest;
use App\Jobs\SaveSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Mockery;
use Tests\TestCase;

class SubmissionsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Event::fake();
    }

    public function test_submit_with_valid_data_dispatches_job_and_event()
    {
        // Arrange
        Queue::fake();
        Event::fake();

        $request = new SubmissionCreateRequest([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello, this is a test message.'
        ]);

        $controller = Mockery::mock(SubmissionsController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        // Act
        $response = $controller->submit($request);

        // Assert
        $this->assertIsArray($response);
        $this->assertEquals([
            'name' => e('John Doe'),
            'email' => e('john@example.com'),
            'message' => e('Hello, this is a test message.'),
        ], $response);

        Queue::assertPushed(SaveSubmission::class, function ($job) {
            $job->handle(); // Manually run the job to trigger the event
            return true;
        });

        Event::assertDispatched(SubmissionSaved::class);
    }

    public function test_submit_with_invalid_data_returns_validation_error()
    {
        // Arrange: Create invalid data
        $data = [
            'name' => '', // Invalid name (required)
            'email' => 'invalid-email', // Invalid email format
            'message' => '', // Invalid message (required)
        ];

        // Act: Simulate a POST request to the submit endpoint
        $response = $this->postJson('/submit', $data);

        // Assert: Check that the response contains validation errors
        $response->assertStatus(422); // Laravel's default status for validation errors
        $response->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_submit_handles_exception_during_job_dispatch()
    {
        // Arrange
        $request = new SubmissionCreateRequest([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hello, this is a test message.'
        ]);

        $controller = Mockery::mock(SubmissionsController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        // Mock the Bus facade to throw an exception when dispatching the job
        Bus::shouldReceive('dispatch')->once()->andThrow(new \Exception('Dispatch error'));

        // Act: Call the controller method
        $response = $controller->submit($request);

        // Assert: Check that the correct error response is returned
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $responseData = $response->getData(true);
        $this->assertEquals('Internal Server Error', $responseData['error']);
        $this->assertEquals(\Exception::class, $responseData['exception_class']);
        $this->assertEquals('Dispatch error', $responseData['exception_message']);
    }
}
