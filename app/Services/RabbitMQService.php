<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AbstractChannel;

class RabbitMQService
{
    protected $connection;
    protected $channel;
    private mixed $user;
    private mixed $pass;
    private mixed $host;
    private mixed $port;
    private string $queueListUrl;
    private mixed $apiHost;
    private mixed $queue;
    private string $consumers;

    /**
     * RabbitMQService constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        // Get configs
        $this->user      = env('RABBIT_USER');
        $this->pass      = env('RABBIT_PASS');
        $this->host      = env('RABBIT_HOST');
        $this->port      = env('RABBIT_PORT');
        $this->apiHost   = env('RABBIT_API_HOST');
        $this->queue     = env('RABBIT_MESSAGE_QUEUE');
        $this->consumers = env('RABBIT_CONSUMERS_LIMIT');

        // API url
        $this->queueListUrl = "{$this->apiHost}/queues/%2F/{$this->queue}";

    }

    /**
     * @throws \Exception
     */
    public function createConnection($isScheduled): void
    {
        try {

            if ($isScheduled) {

                // Create connection
                $this->connection = new AMQPStreamConnection(
                    $this->host, $this->port, $this->user, $this->pass,
                    '/',
                    false,
                    'AMQPLAIN',
                    null,
                    'en_US',
                    160
                );

                // Create channel
                $this->channel = $this->connection->channel();

            } else {
                $this->connection = null;
            }

        } catch (\Exception $e) {

            // Log the exception message
            Log::channel('messages')
                ->error('Error in RabbitMQService constructor: ' . $e->getMessage());

            // Rethrow the exception
            throw $e;
        }
    }

    /**
     * Get the AMQPStreamConnection instance.
     *
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * Get the AMQP channel instance.
     *
     * @return AbstractChannel
     */
    public function getChannel(): AbstractChannel
    {
        return $this->channel;
    }

    /**
     * Publish a message to the specified RabbitMQ queue.
     *
     * @param string $queue
     * @param mixed $message
     * @return void
     */
    public function publishMessage(string $queue, mixed $message): void
    {
        $this->channel->basic_publish(new AMQPMessage($message), '', $queue);
    }

    /**
     * Close the RabbitMQ channel and connection.
     *
     * @throws \Exception
     */
    public function closeConnection(): void
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * Get the number of current consumers of this queue via API.
     *
     * @return int
     * @throws \Exception
     */
    public function getConsumers(): int
    {
        $ch = curl_init($this->queueListUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            // Throw an exception with the error message
            Log::channel('messages')
                ->error('Error checking information about the queues: ' . curl_error($ch));

            throw new \Exception('Error checking information about the queues: ' . $error . ' No consumers up now.');
        }

        curl_close($ch);
        $queueInfo = json_decode($response, true);

        // Check if 'consumers' key exists in the response
        if (isset($queueInfo['consumers'])) {
            return $queueInfo['consumers'];
        } else {
            // Throw an exception if 'consumers' key is not present
            Log::channel('messages')
                ->error('Error: Unable to retrieve the number of consumers.');

            throw new \Exception('Error: Unable to retrieve the number of consumers. No consumers up now.');
        }
    }
}
