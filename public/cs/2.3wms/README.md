![Message Service with RabbitMQ ](http://127.0.0.1:8000/images/cs/rabbitmq-form.png)

## Introduction

- This is my a solution for message processing using RabbitMQ as a content broker provider.

- RabbitMQ is a message broker system that implements the Advanced Message Queuing Protocol (AMQP). It acts as an intermediary between different parts of a system, enabling applications to communicate asynchronously through message exchange.

- RabbitMQ is based on the FIFO (First-In-First-Out) concept for queues. This means that messages in a queue are processed in the order they were received. When a producer sends a message to a queue, it is added to the end of the queue and remains there until a consumer retrieves it for processing.

- In this project, there are Producers (entities that send messages), Consumers (instances that process messages) and both exist as separated entities.

- The exchange of this project is of the type 'Direct' and serves as an entry point for messages in RabbitMQ. Producers send messages to an Exchange, which then routes these messages to appropriate queue(s).

- In Laravel, a producer is considered a 'Job,' which dispatches the message to a RabbitMQ service exchange. The Consumer, in Laravel, is a 'Command' that listen and processes the message.

- Additionally, in Laravel, the concept of a 'Service' is implemented, responsible for creating connections to the RabbitMQ service. Neither the Job nor the Command has any relation to credentials, authentication, or connection creation. All that logic is the responsibility of the service.

## Diagram overview

![git-branch-protection.png](https://jgomes.site/images/diagrams/wms.drawio.png)

## Technical Implementation

#### Controller
```
class MessagesController extends Controller
{
    private Messages $messagesModel;

    public function __construct(Messages $messagesModel)
    {
        $this->messagesModel = $messagesModel;
    }

    public function send(Request $request): JsonResponse
    {
        try {

            // Validate received data
            $validationResult = $this->validateData($request->all());
            if ($validationResult !== true)
            {
               return response()->json(
                    ['error' => $validationResult],
                    422
                );
            }

            // Prepare message
            $message = $this->prepareMessage($request);

            // Send message to the RabbitMQ queue
            $queue = env('RABBIT_MESSAGE_QUEUE');
            dispatch(new MessagesJob(json_encode($message), $queue));

        } catch (\Exception $e) {
            return response()->json(
                ['error' => $e->getMessage()],
                500
            );
        }

        // If flow reaches here, everything worked fine!
        // Confirm if it is an API
        $isApiRequest = $request->is('api/*');
        if ($isApiRequest) {
            return response()->json(['success-api']);
        }

        return response()->json(['success-site']);
    }

    /**
     * Validate the received data using the Messages model.
     *
     * @param array $data
     * @return string|bool
     */
    public function validateData(array $data) : string | bool
    {
        $validator = $this->messagesModel->validateData($data);

        if ($validator->fails()) {

            // Log validation errors
            $errors = $validator->errors()->toArray();
            $this->logError('Validation failed: ' . json_encode($errors));

            // Return errors
            return json_encode($errors);
        }

        return true;
    }

    /**
     * Prepare the message data from the request.
     *
     * @param Request $request
     * @return array
     */
    private function prepareMessage(Request $request): array
    {
        return [
            'name'    => $request->input('name'),
            'email'   => $request->input('email'),
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
        ];
    }

    /**
     * Log an error message to the 'messages' channel.
     *
     * @param string $message
     * @return void
     */
    private function logError(string $message): void
    {
        Log::channel('messages')
            ->error('Error on Controller receiving message from client: ' . $message);
    }
}
```

#### Modal
```
class Messages extends Model
{
    protected $table    = 'messages';
    public $timestamps  = false;
    protected $fillable = [
        "name",
        "email",
        "subject",
        "content",
        "created_at"
    ];

    /**
     * @param array $data
     * @return Validator
     */
    public static function validateData(array $data): Validator
    {
        // Define validation rules
        $rules = [
            'name'    => 'required|string|max:50',
            'email'   => 'required|email|max:50',
            'subject' => 'nullable|string|max:100',
            'content' => 'required|string|max:3000',
        ];

        return Validatior2::make($data, $rules);
    }
}
```

#### Service
```
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
    private mixed $consumers;

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
```

#### Job
```
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
```

#### Command
```
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
     * Handle validation failure by 8logging the error.
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
```

## Demonstration ( Click on the image to see the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=Dn90_FnmiAA)
