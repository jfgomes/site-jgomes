<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PipelineEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $result;
    private string $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($result, $url)
    {
        $this->result = $result;
        $this->url    = $url;
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
                'result' => $this->result,
                'url'    => $this->url
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
