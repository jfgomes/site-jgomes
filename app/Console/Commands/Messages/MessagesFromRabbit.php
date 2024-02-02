<?php

namespace App\Console\Commands\Messages;

use App\Mail\MessageEmail;
use App\Mail\RabbitEmail;
use App\Models\Messages;
use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessagesFromRabbit extends Command
{
    protected $signature   = 'queue:messages {--is-scheduled=}';
    protected $description = 'Messages from RabbitMQ queue and stored at DB';

    private mixed $queue;
    private mixed $consumers;
    private mixed $channel;
    private RabbitMQService $rabbitMQService;

    /**
     * @throws \Exception
     */
    public function __construct(RabbitMQService $rabbitMQService)
    {
        parent::__construct();

        $this->rabbitMQService = $rabbitMQService;

        // Get missing settings according the env
        $this->queue     = env('RABBIT_MESSAGE_QUEUE');
        $this->consumers = env('RABBIT_CONSUMERS_LIMIT');
    }

    /**
     * Job starts here.
     * @return bool
     * @throws \Exception
     */
    public function handle(): bool
    {
        // Check the number of consumers up. If it reaches the limit, don't need to create more. Abort here.
        if ($this->rabbitMQService->getConsumers() >= $this->consumers) {
            $this->info("All total $this->consumers consumers are running. No more consumers needed.");
            return false;
        }

        $this->rabbitMQService->createConnection((bool)$this->option('is-scheduled'));

        // Init the new listener
        $this->init();

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
     * Start a new listener.
     * @return void
     */
    private function init(): void
    {
        try {
            $this->setCloseOnDestruct();
            $this->startChannel();
            $this->startQueueDeclare();
            $this->info("Init listener done.");
        } catch (\Exception $ex) {
            $this->handleInitException($ex);
        }
    }

    /**
     * Set close_on_destruct to false.
     *
     * @return void
     */
    private function setCloseOnDestruct(): void
    {
        $this->info("Set close_on_destruct..");
        $this->rabbitMQService->getConnection()->set_close_on_destruct(false);
    }

    /**
     * Start the channel.
     *
     * @return void
     */
    private function startChannel(): void
    {
        $this->info("Start channel..");
        $this->channel = $this->rabbitMQService->getChannel();
    }

    /**
     * Start queue declaration.
     *
     * @return void
     */
    private function startQueueDeclare(): void
    {
        $this->info("Start queue_declare..");
        $this->channel->queue_declare(
            $this->queue,
            false,
            true,
            false,
            false
        );
    }

    /**
     * Handle the exception during initialization.
     *
     * @param \Exception $ex
     * @return void
     */
    private function handleInitException(\Exception $ex): void
    {
        $this->error($ex->getMessage());

        // Log the error
        Log::channel('messages')
            ->error('Error on Console processing a message from rabbit: ' . $ex->getMessage());
    }


    /**
     * Start a new consumer.
     * @param $callback
     * @return void
     */
    private function  consumeQueue($callback): void
    {
        // Ensure that the channel is initialized
        if (!$this->channel) {
            $this->handleChannelNotInitialized();
            return;
        }

        // Set up basic consumption with the provided callback
        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        // Continue consuming messages until the channel stops
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * Handle the case when the channel is not initialized.
     *
     * @return void
     */
    private function handleChannelNotInitialized(): void
    {
        $composedError = 'Channel not initialized.';
        $this->error($composedError);

        // Log the error
        Log::channel('messages')
            ->error('Error on Console processing a message from rabbit: ' . $composedError);
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
     * @throws \Throwable
     */
    private function saveMessage($originalData): bool
    {
        // Decode the JSON data
        $data = json_decode($originalData, true);

        try {

            // Validate the data using the Messages model
            $validator = Messages::validateData($data);

            // Check if validation fails
            if ($validator->fails()) {

                // Handle validation failure
                $this->handleValidationFailure($validator, $originalData);

            } else {

                // Save the validated data to the database
                $this->saveMessageToDatabase($data, $originalData);
            }

        } catch (\Throwable $e) {

            // Send mail notification with the the exception
            Log::channel('messages')
                ->error($e->getMessage());

            // Send email with the fail msg
            Mail::to(env('MAIL_USERNAME'))
                ->send(new RabbitEmail($originalData, $e->getMessage()));
        }

        return true;
    }


    /**
     * Handle validation failure by logging the error.
     *
     * @param Validator $validator
     * @param string $originalData
     * @return void
     */
    private function handleValidationFailure(Validator $validator, string $originalData): void
    {
        $errors        = $validator->errors()->toArray();
        $composedError = "\nValidation failed: " . json_encode($errors) .
            "\nOriginal message: " . $originalData;

        // I/O
        $this->error($composedError);

        // Log the error
        Log::channel('messages')
            ->error('Error processing a message from rabbit: ' . $composedError);

        // Send email with the fail msg
        Mail::to(env('MAIL_USERNAME'))
            ->send(new RabbitEmail(json_encode($originalData), $composedError));
    }

    /**
     * Save the validated data to the database and log the success.
     *
     * @param array $data
     * @param string $originalData
     * @return void
     */
    private function saveMessageToDatabase(array $data, string $originalData): void
    {
        // Create a new message in the database
        $message = Messages::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'subject'    => $data['subject'] ?? null,
            'content'    => $data['content'],
            'created_at' => now()
        ]);

        // Log the success message in I/O
        $this->info(
            "\nMessage {$originalData} \n- Sent from queue:messages."
            . "\n- Saved in the database with ID: {$message->id}."
        );

        // Log the success message in file
        Log::channel('messages')
            ->info("Message {$originalData} sent and saved in the database with ID: {$message->id}");

        // Send email
        Mail::to(env('MAIL_USERNAME'))
            ->send(new MessageEmail($data));

        // Log email sent in file
        Log::channel('emails')
            ->info("Email sent with {$originalData} to " . env('MAIL_USERNAME') . " | DB ID: {$message->id}");
    }

    /**
     * End connection and consumer.
     * @return void
     * @throws \Exception
     */
    private function closeConnection(): void
    {
        try {
            $this->rabbitMQService->closeConnection();
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }
    }
}
