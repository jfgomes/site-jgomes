<?php

namespace App\Console\Commands\Pipeline;

use App\Mail\MessageEmail;
use App\Mail\PipelineEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PipelineResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipeline:result {--result=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send an e-mail with the result of each pipeline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $result = $this->option('result');

        if ($result === 'ok') {
            $this->info('Pipeline result is OK!');
        } else {
            $this->error('Unknown pipeline result!');
        }

        // Send email
        Mail::to(env('MAIL_USERNAME'))
            ->send(new PipelineEmail($result));

        return 0;
    }
}
