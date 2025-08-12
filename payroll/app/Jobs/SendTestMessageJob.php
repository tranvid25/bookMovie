<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTestMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $message;
    public function __construct($message)
    {
        $this->message=$message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('RabbitMQ Job Received:', ['message' => $this->message]);
    }
}
