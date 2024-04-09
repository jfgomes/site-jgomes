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
    protected RabbitMQService $rabbitMQService;

    /**
     * Create a new job instance.
     *
     * @param mixed $data The message data to be published to RabbitMQ.
     * @param string $queue The RabbitMQ queue name.
     * @param RabbitMQService $rabbitMQService The RabbitMQ service instance.
     */
    public function __construct(mixed $data, string $queue, RabbitMQService $rabbitMQService)
    {
        $this->data  = $data;
        $this->queue = $queue;
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        // Use the injected RabbitMQService instance
        $this->rabbitMQService->createConnection(true);
        $this->rabbitMQService->publishMessage($this->queue, $this->data);
        $this->rabbitMQService->closeConnection();
    }
}
