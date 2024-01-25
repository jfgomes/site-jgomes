<?php

namespace App\Console;

use App\Models\Messages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessagesFromRabbit extends Command
{
    protected $signature   = 'queue:messages';
    protected $description = 'Messages read from RabbitMQ queue and stored at DB';

    private $user;
    private $pass;
    private $host;
    private $port;
    private $apiHost;
    private $queue;
    private $connection;
    private $consumers;
    private $channel;
    private $queueListUrl;

    public function __construct()
    {
        parent::__construct();

        // Get settings according the env
        $this->user      = env('RABBIT_USER');
        $this->pass      = env('RABBIT_PASS');
        $this->host      = env('RABBIT_HOST');
        $this->port      = env('RABBIT_PORT');
        $this->apiHost   = env('RABBIT_API_HOST');
        $this->queue     = env('RABBIT_MESSAGE_QUEUE');
        $this->consumers = env('RABBIT_CONSUMERS_LIMIT');

        // Build the API host
        $this->queueListUrl = "{$this->apiHost}/queues/%2F/{$this->queue}";
    }

    /**
     * Job starts here.
     * @return bool
     * @throws \Exception
     */
    public function handle(): bool
    {
        // Check the number of consumers up. If it reach the limit, don't need to create more. Abort here.
        if ($this->getConsumers() >= $this->consumers) {
            $this->info("All total $this->consumers consumers are running. No more consumers needed.");
            return false;
        }

        // Init new connection
        $this->initConnection();

        // Init new consumer
        $this->consumeQueue(function ($msg) {
            $result = $this->saveMessage($msg->body);
            if($result)
                $msg->ack();
        });

        // Close connections and consumers
        $this->closeConnection();

        return true;
    }

    /**
     * Get the number of current connections of this queue via API.
     * @return int
     */
    private function getConsumers(): int
    {
        $ch = curl_init($this->queueListUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            $this->error('Error checking information about the queues: ' . $error);
            return 0;
        }

        curl_close($ch);
        $queueInfo = json_decode($response, true);
        return $queueInfo['consumers'];
    }

    /**
     * Start a new connection.
     * @return void
     */
    private function initConnection(): void
    {
        try {

            $this->info("Start connection..");
            $this->connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->pass,
                '/',
                false,
                'AMQPLAIN',
                null,
                'en_US',
                160,
            );

            $this->info("Set close_on_destruct..");
            $this->connection->set_close_on_destruct(false);

            $this->info("Start channel..");
            $this->channel = $this->connection->channel();

            $this->info("Start queue_declare..");
            $this->channel->queue_declare(
                $this->queue,
                false,
                true,
                false,
                false
            );

            $this->info("InitConnection done..");
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            return;
        }
    }

    /**
     * Start a new consumer.
     * @param $callback
     * @return void
     */
    private function consumeQueue($callback): void
    {
        // Ensure $this->channel is not null before using it
        if (!$this->channel) {
            $this->error('Channel not initialized.');
            return;
        }

        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * Read message from the queue and store it in DB.
     *
     * @param $originalData - Message in this format:
     *                  {
     *                     "name"    : "name",
     *                     "email"   : "to@to.to",
     *                     "subject" : "subject",
     *                     "content" : "message"
     *                   }
     * @return bool - true if the message is well delivered.
     *                false if there's some problem with the message.
     */
    private function saveMessage($originalData): bool
    {
        // Extract data
        $data = json_decode($originalData, true);

        // Define validation rules
        $rules = [
            'name'    => 'required|string|max:50',
            'email'   => 'required|email|max:50',
            'subject' => 'nullable|string|max:100',
            'content' => 'required|string|max:255',
        ];

        // Create a Validator object
        $validator = Validator::make($data, $rules);

        // Check if validation fails
        if ($validator->fails())
        {
            $errors = $validator->errors()->toArray();
            $this->error(
                " \n #################################### "
                . " \n\n Validation failed: " . json_encode($errors)
                . " \n\n Original message: " . $originalData
                . " \n\n ################################### \n"
            );

            return false;
        }

        // If validation passes, save data to the database
        $message = Messages::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'subject'    => $data['subject'] ?? null,
            'content'    => $data['content'],
            'created_at' => now()
        ]);

        $this->info(
            "\n Message {$originalData} \n- Sent from queue:messages."
            . "\n- Saved in the database with ID: {$message->id}."
        );

        return true;
    }

    /**
     * End connection and consumer.
     * @return void
     * @throws \Exception
     */
    private function closeConnection(): void
    {
        // Check if the channel ($this->channel) is not null before attempting to close
        if ($this->channel) {
            // Close the channel only if it is open
            $this->channel->close();
        }

        // Check if the connection ($this->connection) is not null or already closed
        if ($this->connection && $this->connection->isConnected()) {
            // Close the connection only if it is connected
            $this->connection->close();
        }
    }
}
