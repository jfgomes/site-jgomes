<?php

namespace Tests\Unit\Mail;

use App\Mail\RabbitEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RabbitEmailTest extends TestCase
{
    public function testBuildEmail()
    {
        // Dados de exemplo
        $data = 'RabbitMQ Data';
        $error = 'Error occurred';

        // Criar uma nova instância do email
        $email = new RabbitEmail($data, $error);

        // Construir o email
        $builtEmail = $email->build();

        // Verificar se o e-mail foi construído corretamente
        $this->assertInstanceOf(RabbitEmail::class, $builtEmail);
        //$this->assertEquals(env('MAIL_USERNAME'), $builtEmail->from[0]['address']);
        //$this->assertEquals(env('MAIL_FROM_NAME'), $builtEmail->from[0]['name']);
        $this->assertEquals('Rabbit notice', $builtEmail->subject);
       // $this->assertEquals('mail.rabbit', $builtEmail->view);
        $this->assertEquals($data, $builtEmail->viewData['email_data']);
        $this->assertEquals($error, $builtEmail->viewData['email_error']);
    }

    public function testSendEmail()
    {
        // Simular o envio de e-mail
        Mail::fake();

        // Dados de exemplo
        $data = 'RabbitMQ Data';
        $error = 'Error occurred';

        // Criar uma nova instância do email
        $email = new RabbitEmail($data, $error);

        // Enviar o e-mail
        Mail::send($email);

        // Verificar se o e-mail foi enviado
        Mail::assertSent(RabbitEmail::class, function ($mail) use ($data, $error) {
            return $mail->email_data === $data &&
                $mail->email_error === $error;
        });
    }
}
