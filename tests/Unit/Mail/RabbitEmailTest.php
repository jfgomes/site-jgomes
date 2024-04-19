<?php

namespace Tests\Unit\Mail;

use App\Mail\RabbitEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RabbitEmailTest extends TestCase
{
    public function testBuildEmail()
    {
        // Sample data
        $data = 'RabbitMQ Data';
        $error = 'Error occurred';

        // Create a new instance of the email
        $email = new RabbitEmail($data, $error);

        // Build the email
        $builtEmail = $email->build();

        // Verify if the email was built correctly
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
        // Simulate sending email
        Mail::fake();

        // Sample data
        $data = 'RabbitMQ Data';
        $error = 'Error occurred';

        // Create a new instance of the email
        $email = new RabbitEmail($data, $error);

        // Send the email
        Mail::send($email);

        // Verify if the email was sent
        Mail::assertSent(RabbitEmail::class, function ($mail) use ($data, $error) {
            return $mail->email_data === $data &&
                $mail->email_error === $error;
        });
    }
}
