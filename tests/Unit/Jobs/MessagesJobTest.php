<?php

namespace Tests\Unit\Jobs;

use App\Jobs\MessagesJob;
use App\Services\RabbitMQService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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

        // Configurar o mock para esperar as chamadas apropriadas
        $rabbitMQService->expects($this->once())
            ->method('createConnection')
            ->with(true);

        $rabbitMQService->expects($this->once())
            ->method('publishMessage')
            ->with('queue_name', 'message_data');

        $rabbitMQService->expects($this->once())
            ->method('closeConnection');

        // Criar uma nova instância de MessagesJob com o mock do RabbitMQService
        $job = new MessagesJob('message_data', 'queue_name', $rabbitMQService);

        // Chamar o método handle para executar o job
        $job->handle();
    }

}
