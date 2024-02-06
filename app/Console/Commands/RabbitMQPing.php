<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQPing extends Command
{
    protected $signature   = 'rabbitmq:ping';
    protected $description = 'Ping RabbitMQ server';

    public function handle(): void
    {
        try {

            $user = env('RABBIT_USER');
            $pass = env('RABBIT_PASS');
            $host = env('RABBIT_HOST');
            $port = env('RABBIT_PORT');

            $connection = new AMQPStreamConnection($host, $port, $user, $pass);
            $connection->close();

            $this->info('Successfully pinged RabbitMQ server!');
        } catch (\Exception $e) {
            $this->error('Failed to ping RabbitMQ server. Error: ' . $e->getMessage());
        }
    }
}
