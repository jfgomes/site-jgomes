<?php

namespace Tests\Unit\Jobs;

use App\Jobs\MessagesJob;
use App\Services\RabbitMQService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagesJobTest extends TestCase
{
    use RefreshDatabase;

    public function testJobPublishesMessageToRabbitMQ()
    {
        // Mock RabbitMQService
        $rabbitMQService = $this->getMockBuilder(RabbitMQService::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up the mock to expect the appropriate calls
        $rabbitMQService->expects($this->once())
            ->method('createConnection')
            ->with(true);

        $rabbitMQService->expects($this->once())
            ->method('publishMessage')
            ->with('queue_name', 'message_data');

        $rabbitMQService->expects($this->once())
            ->method('closeConnection');

        // Create a new instance of MessagesJob with the RabbitMQService mock
        $job = new MessagesJob('message_data', 'queue_name', $rabbitMQService);

        // Call the handle method to execute the job
        $job->handle();
    }

}
