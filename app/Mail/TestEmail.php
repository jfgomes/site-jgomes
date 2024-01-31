<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //--
    }

    /**
     * Get the message content definition.
     *
     * @return TestEmail
     */
    public function build(): TestEmail
    {
        return $this->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'))
            ->subject('Test')
            ->view('mail.test');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
