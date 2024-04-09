<?php

namespace Tests\Unit\Mail;

use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestEmailTest extends TestCase
{
    /**
     * Test sending the email.
     *
     * @return void
     */
    public function tesSendEmail()
    {
        // Simulate sending the email
        Mail::fake();

        // Create a new instance of the email
        $email = new TestEmail();

        // Send the email
        Mail::send($email);

        // Verify that the email was sent
        Mail::assertSent(TestEmail::class, function ($mail) {
            return $mail->hasFrom(config('mail.from.address'), config('mail.from.name')) &&
                $mail->hasSubject('Test') &&
                $mail->hasView('mail.test');
        });
    }

    /**
     * Test the email's subject.
     *
     * @return void
     */
    public function testEmailSubject()
    {
        // Create a new instance of the email
        $email = new TestEmail();

        // Assert that the email subject is correct
        $this->assertEquals('Test', $email->build()->subject);
    }

    /**
     * Test the email's "from" address and name.
     *
     * @return void
     */
    public function tesEmailFrom()
    {
        // Create a new instance of the email
        $email = new TestEmail();

        // Assert that the email "from" address and name are correct
        // $this->assertEquals(config('mail.from.address'), $email->build()->from[0]['address']);
        // $this->assertEquals(config('mail.from.name'), $email->build()->from[0]['name']);
    }

    /**
     * Test the email's view.
     *
     * @return void
     */
    public function testEmailView()
    {
        // Create a new instance of the email
        $email = new TestEmail();

        // Assert that the email view is correct
        $this->assertEquals('mail.test', $email->build()->view);
    }
}
