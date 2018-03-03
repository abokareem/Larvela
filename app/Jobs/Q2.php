<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Q3;


class Q2 implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
		echo "Q2 Constructor".PHP_EOL;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		echo "Q2 Handle()".PHP_EOL;
		dispatch(new Q3());
    }
}
