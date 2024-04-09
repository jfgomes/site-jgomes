<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use App\Console\Commands\RabbitMQPing;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Console\OutputStyle;
use PhpAmqpLib\Connection\AMQPStreamConnection;





class RabbitMQPingTest extends TestCase
{


    /** @test */
   /* public function it_pings_rabbitmq_server_successfully()
    {
        // Mock do AMQPStreamConnection
        $mockConnection = $this->createMock(\PhpAmqpLib\Connection\AMQPStreamConnection::class);
        $mockConnection->expects($this->once())
            ->method('close');

        // Mock do comando com injeção de dependência do AMQPStreamConnection
        $command = new \App\Console\Commands\RabbitMQPing($mockConnection);

        // Configuração da saída para o teste
        $output = new BufferedOutput();

        // Definir a saída do comando
        $command->setOutput($output);

        // Definir as configurações do RabbitMQ para o teste
        config(['rabbitmq.connections.default.host' => 'testhost']);
        config(['rabbitmq.connections.default.port' => 5672]);
        config(['rabbitmq.connections.default.user' => 'testuser']);
        config(['rabbitmq.connections.default.pass' => 'testpass']);

        // Executar o comando
        $command->handle();

        // Verificar a saída
        $this->assertStringContainsString('Successfully pinged RabbitMQ server!', $output->fetch());
    }
*/


    /** @test */
    public function it_handles_connection_failure_gracefully()
    {
        // Mock do comando sem injeção de dependência do AMQPStreamConnection
        $command = new RabbitMQPing();

        // Configuração da saída para o teste
        $output = new BufferedOutput();

        // Definir a saída do comando
        $command->setOutput(new OutputStyle(new ArrayInput([]), $output));

        // Definir as configurações do RabbitMQ para o teste
        config(['rabbitmq.connections.default.host' => 'invalidhost']);
        config(['rabbitmq.connections.default.port' => 5672]);
        config(['rabbitmq.connections.default.user' => 'testuser']);
        config(['rabbitmq.connections.default.pass' => 'testpass']);

        // Executar o comando
        $command->handle();

        // Verificar a saída
        $this->assertStringContainsString('Failed to ping RabbitMQ server.', $output->fetch());
    }
}
