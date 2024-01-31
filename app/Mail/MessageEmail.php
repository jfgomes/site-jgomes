<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $email_name;

    private ?string $email_address;

    private string $email_subject;

    private string $email_body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->email_name    = $data['name'];
        $this->email_address = $data['email'];
        $this->email_subject = $data['subject'] ?? '';
        $this->email_body    = $data['content'];
    }

    /**
     * Get the message content definition.
     *
     * @return MessageEmail
     */
    public function build(): MessageEmail
    {
        return $this->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'))
            ->subject('New message received')
            ->view('mail.message')
            ->with([
                'email_name'    => $this->email_name,
                'email_address' => $this->email_address,
                'email_subject' => $this->email_subject,
                'email_body'    => $this->email_body
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
