<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RabbitEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $email_data;

    private string $email_error;

    /**
     * Create a new message instance.
     *
     * @param string $data
     * @param string $error
     *
     */
    public function __construct(string $data, string $error = '')
    {
        $this->email_data  = $data;
        $this->email_error = $error;
    }

    /**
     * Get the message content definition.
     *
     * @return RabbitEmail
     */
    public function build(): RabbitEmail
    {
        return $this->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'))
            ->subject('Rabbit notice')
            ->view('mail.rabbit')
            ->with([
                'email_data'  => $this->email_data,
                'email_error' => $this->email_error
            ]);
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
