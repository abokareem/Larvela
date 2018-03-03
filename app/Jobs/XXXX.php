<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class XXXX implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
		echo "Im the XXXX contructor!";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		echo "Im the XXXX handler()!";
    }
}
