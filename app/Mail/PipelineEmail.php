<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PipelineEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $result;
    private string $msg;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($result, $msg)
    {
        $this->result = $result;
        $this->msg    = $msg;
    }

    /**
     * Get the message content definition.
     *
     * @return PipelineEmail
     */
    public function build(): PipelineEmail
    {
        return $this->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'))
            ->subject($this->result)
            ->view('mail.pipeline')
            ->with([
                'result' => $this->result,
                'msg'    => str_replace("\nbash:", '<br>', $this->msg)
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
