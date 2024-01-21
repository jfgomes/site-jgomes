<?php

namespace App\Console;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessagesFromRabbit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'queue:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Messages read from rabbitmq queue and stored at db';

    /**
     * @var string
     */
    private $_user;

    /**
     * @var string
     */
    private $_pass;

    /**
     * @var string
     */
    private $_queue;

    /**
     * @var string
     */
    private $_host;

    /**
     * @var string
     */
    private $_api_port;

    /**
     * @var string
     */
    private $_port;

    /**
     * @var string
     */
    private $_queue_list_url;

    /**
     * @var string
     */
    private $_connection;

    /**
     * @var string
     */
    private $_channel;

    /**
     * @var string
     */
    private $_num_of_consumers;

    public function __construct()
    {
        parent::__construct();

        // User credentials:
        $this->_user  = 'user';
        $this->_pass  = 'user';

        // Connection data:
        $this->_host      = 'localhost';
        $this->_port      = '5674';
        $this->_api_port  = '15674';
        $this->_queue     = 'messages_prod';

        // Rabbit MQ api endpoint to get queue info:
       //$this->_queue_list_url = "$this->_host:$this->_api_port/api/queues/%2F/$this->_queue";

        $this->_queue_list_url = "https://jgomes.site/rabbit2/api/queues/%2F/messages_prod"   ;

        $this->_num_of_consumers = 4;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        if ($this->getConsumers() > $this->_num_of_consumers)
        {
            echo "All total consumers are running\n";
            exit;
        }

        $this->initConnection();
        $callback = function ($msg) use (&$receivedChunks, &$totalChunks) {
            $this->saveMessage($msg->body);
            $msg->ack();
        };

        $this->consumeQueue($callback);
        $this->closeConnection();

        return true;
    }

    /**
     * @return mixed
     */
    private function getConsumers()
    {
        $ch = curl_init($this->_queue_list_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->_user:$this->_pass");
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            dd('Error checking information about the queues.');
        }

        $queueInfo = json_decode($response, true);
        return $queueInfo['consumers'];
    }

    /**
     * @return void
     */
    private function initConnection()
    {
        try {
            /*
                       $sslContext = [
                           'local_cert' => '/home/jgomes/my/jgomes/cert/jgomes_site.crt', // certificado
                           'local_pk' => '/home/jgomes/my/jgomes/cert/jgomes_site.key', // chave privada
                           'cafile' => '/home/jgomes/my/jgomes/cert/jgomes_site.ca-bundle', // bundle de CA
                       ];

                       $this->_connection = new AMQPStreamConnection(
                           'jgomes.site',
                           5674,
                           $this->_user,
                           $this->_pass,
                           '/',
                           false,
                           'AMQPLAIN',
                           null,
                           'en_US',
                           160,
                           null, // read_write_timeout
                           null, //stream_context_create(['ssl' => $sslContext]), // context
                           false, // keepalive
                           0, // heartbeat
                           null // channel_rpc_timeout
                       );

                       echo "Set_close_on_destruct..\n";
                       $this->_connection->set_close_on_destruct(false);

                       echo "Start channel..\n";
                       $this->_channel = $this->_connection->channel();

                       echo "Start queue_declare..\n";
                       $this->_channel->queue_declare(
                           $this->_queue,
                           false,
                           true,
                           false,
                           false
                       );

                       echo "InitConnection done..\n";
           */
             echo "Start connection..\n";
                       $this->_connection = new AMQPStreamConnection(
                         'jgomes.site',
                           5674,
                           $this->_user,
                           $this->_pass,
                           '/',
                           false,
                           'AMQPLAIN',
                           null,
                           'en_US',
                           160,
                       );

                       echo "Set_close_on_destruct..\n";
                       $this->_connection->set_close_on_destruct(false);

                       echo "Start channel..\n";
                       $this->_channel = $this->_connection->channel();

                       echo "Start queue_declare..\n";
                       $this->_channel->queue_declare(
                           $this->_queue,
                           false,
                           true,
                           false,
                           false
                       );

                       echo "InitConnection done..\n";
        }catch (\Exception $ex){
            dd($ex->getMessage());
        }
    }

    /**
     * @param $callback
     * @return void
     */
    private function consumeQueue($callback)
    {
        $this->_channel->basic_consume(
            $this->_queue,
            '',
            false,
            false,
            false,
            false,
            $callback
        );

        while ($this->_channel->is_consuming()) $this->_channel->wait();
    }

    private function saveMessage($data)
    {
        var_dump($data);
    }

    /**
     * @return void
     */
    private function closeConnection()
    {
        $this->_channel->close();
        $this->_connection->close();
    }
}
