<?php

namespace Tests\Unit\Mail;

use App\Mail\MessageEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MessageEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testBuildEmail()
    {
        // Dados de teste para construir o e-mail
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content',
        ];

        // Criar uma instância do e-mail
        $email = new MessageEmail($data);

        // Construir o e-mail
        $builtEmail = $email->build();

        // Verificar se o e-mail foi construído corretamente
        $this->assertInstanceOf(MessageEmail::class, $builtEmail);
        // $this->assertEquals(env('MAIL_USERNAME'), $data['email']);
        // $this->assertEquals(env('MAIL_FROM_NAME'), $builtEmail->from[0]['name']);
        $this->assertEquals('New message received', $builtEmail->subject);
        //  $this->assertEquals('mail.message', $builtEmail->viewData['view']);
        $this->assertEquals($data['name'], $builtEmail->viewData['email_name']);
    }


}
