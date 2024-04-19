<?php

namespace Tests\Unit\Mail;

use App\Mail\MessageEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PipelineEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testBuildEmail()
    {
        // Test data to build the email
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'content' => 'Test Content',
        ];

        // Create an instance of the email
        $email = new MessageEmail($data);

        // Build the email
        $builtEmail = $email->build();

        // Verify if the email was built correctly
        $this->assertInstanceOf(MessageEmail::class, $builtEmail);
        // $this->assertEquals(env('MAIL_USERNAME'), $data['email']);
        // $this->assertEquals(env('MAIL_FROM_NAME'), $builtEmail->from[0]['name']);
        $this->assertEquals('New message received', $builtEmail->subject);
        //  $this->assertEquals('mail.message', $builtEmail->viewData['view']);
        $this->assertEquals($data['name'], $builtEmail->viewData['email_name']);
    }
}
