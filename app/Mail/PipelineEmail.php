<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PipelineEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $result;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Get the message content definition.
     *
     * @return PipelineEmail
     */
    public function build(): PipelineEmail
    {
        return $this->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'))
            ->subject('Pipeline result')
            ->view('mail.pipeline')
            ->with([
                'result' => $this->result
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
