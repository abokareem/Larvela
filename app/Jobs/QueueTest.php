<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Q2;
use App\Jobs\Q3;


class QueueTest extends Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
		echo "Constructed<br/>";
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
echo "Handled!\n".PHP_EOL;
		dispatch(new Q2());
    }
}
