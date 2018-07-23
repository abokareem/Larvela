<?php
/**
 * \class	BackInStock
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-12-06
 *
 * [CC]
 *
 * \addtogroup ProductReplenishment
 * BackInStock - Provide a point before a Customer is notified that the product is back in stock.
 * - Customer may not be in Customer table.
 * - Could use this as a point to send out additional recommendations?
 *
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;





/**
 * \brief Called prior to a customer being notified of a product back in stock.
 */
class BackInStock implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;



/**
 * The Store object
 * @var mixed $store
 */
protected $store;


/**
 * The email address to send any emails to.
 * @var string $email
 */
protected $email;


/**
 * The Product that is now back in stock.
 * @var mixed	$product
 */
protected $product;





    /**
     * Create a new job instance 
	 *
     * @param 	mixed	$store	The Store object.
     * @param	string	$email	email address of customer.
     * @param 	mixed	$product	The Product that has come back into stock.
     * @return	void
     */
    public function __construct($store, $email, $product)
    {
		$this->store = $store;
		$this->email = $email;
		$this->product = $product;
    }



    /**
     * Place Holder for any additonal Business Logic when a product comes back in stock.
	 *
     * @return void
     */
    public function handle()
    {
    }
}
