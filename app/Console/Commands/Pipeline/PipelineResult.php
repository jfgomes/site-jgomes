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
    protected $signature = 'pipeline:result {--result=} {--msg=}';

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
        $result  = $this->option('result');
        $message = $this->option('msg');

        if ($result === 'ok')
        {
            $this->info($result = "Pipeline completed with success!");
            $message = "Everything is in place.. up and running in production!";
        }
        else
        {
            $this->error($result = "Pipeline failed!");
        }

        // Send Jenkins notification
        Mail::to(env('MAIL_USERNAME'))
            ->send(new PipelineEmail($result, $message));

        return 0;
    }
}
