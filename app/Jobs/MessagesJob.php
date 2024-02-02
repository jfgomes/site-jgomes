<?php

namespace App\Jobs;

use App\Services\RabbitMQService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MessagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected mixed $data;
    public $queue;

    /**
     * Create a new job instance.
     *
     * @param mixed $data The message data to be published to RabbitMQ.
     * @param string $queue The RabbitMQ queue name.
     */
    public function __construct(mixed $data, string $queue)
    {
        $this->data  = $data;
        $this->queue = $queue;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        // Create a new instance of RabbitMQService
        $rabbitMQService = new RabbitMQService();
        $rabbitMQService->createConnection(true);

        // Publish the message to the specified RabbitMQ queue
        $rabbitMQService->publishMessage($this->queue, $this->data);

        // Close the RabbitMQ connection
        $rabbitMQService->closeConnection();
    }
}
