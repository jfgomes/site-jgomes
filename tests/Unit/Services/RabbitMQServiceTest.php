<?php

namespace Tests\Unit\Services;

use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Log;
use Mockery;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class RabbitMQServiceTest extends TestCase
{
    private $rabbitMQService;
    private $mockConnection;
    private $mockChannel;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the AMQPStreamConnection
        $this->mockConnection = Mockery::mock(AMQPStreamConnection::class);

        // Mock the AMQP channel
        $this->mockChannel = Mockery::mock();

        // Inject the mocked connection and channel into RabbitMQService
        $this->rabbitMQService = new RabbitMQService();
        $this->rabbitMQService->setConnection($this->mockConnection);
        $this->rabbitMQService->setChannel($this->mockChannel);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @throws \Exception
     */
    public function tesCreateConnection()
    {
        // Expect the channel to be created
        $this->mockConnection->shouldReceive('channel')->andReturn($this->mockChannel);

        // Call the method to create connection
        $this->rabbitMQService->createConnection(true, $this->mockConnection);

        // Assert that the connection was not created internally
        $this->assertNull($this->rabbitMQService->getConnection());
        $this->assertInstanceOf(Mockery\MockInterface::class, $this->rabbitMQService->getChannel());
    }

    public function testPublishMessage()
    {
        $queue = 'test_queue';
        $message = 'test_message';

        // Expect the publish method to be called
        $this->mockChannel->shouldReceive('basic_publish')
            ->with(Mockery::type(AMQPMessage::class), '', $queue)
            ->once();

        // Call the method to publish message
        $this->rabbitMQService->publishMessage($queue, $message);

        // Assert that the method call occurred
        $this->assertTrue(true);
    }
}
