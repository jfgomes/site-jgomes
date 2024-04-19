<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\RabbitMQPing;
use Illuminate\Support\Facades\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tests\TestCase;

class RabbitMQPingTest extends TestCase
{
    public function tesItPingsRabbitMQServerSuccessfully()
    {
        // Mock do AMQPStreamConnection
        $mockConnection = $this->getMockBuilder(AMQPStreamConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockConnection->expects($this->once())
            ->method('close');

        // Criar instância do comando com injeção de dependência
        $command = new RabbitMQPing($mockConnection);

        // Configuração da saída para o teste
        $output = new BufferedOutput();

        // Definir a saída do comando
        $command->setOutput(new SymfonyStyle(new ArrayInput([]), $output));

        // Definir as configurações do RabbitMQ para o teste
        Config::set('env.RABBIT_USER', 'testuser');
        Config::set('env.RABBIT_PASS', 'testpass');
        Config::set('env.RABBIT_HOST', 'testhost');
        Config::set('env.RABBIT_PORT', 5673);

        // Executar o comando
        $command->handle();

        // Verificar a saída
        $this->assertStringContainsString('Successfully pinged RabbitMQ server!', $output->fetch());
    }

    public function tesItHandlesConnectionFailureGracefully()
    {
        // Criar instância do comando
        $command = new RabbitMQPing();

        // Configuração da saída para o teste
        $output = new BufferedOutput();

        // Definir a saída do comando
        $command->setOutput(new SymfonyStyle(new ArrayInput([]), $output));

        // Definir as configurações do RabbitMQ para o teste
        Config::set('env.RABBIT_USER', 'testuser');
        Config::set('env.RABBIT_PASS', 'testpass');
        Config::set('env.RABBIT_HOST', 'invalidhost'); // Host inválido
        Config::set('env.RABBIT_PORT', 5673); // Porta inválida

        // Executar o comando
        $command->handle();

        // Verificar a saída
        $this->assertStringContainsString('Failed to ping RabbitMQ server.', $output->fetch());
    }
}
