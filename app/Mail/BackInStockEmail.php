<?php
/**
 * \class	BackInStockEmail
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2018-07-19
 *
 * [CC]
 *
 * \addtogroup Product_Replenishment
 * BackInStockEmail - Return a templated email notifying customer that the product is back in stock.
 * - uses Blade to implement templated email.
 * - See related BackInStock Job for related business logic for additional processing
 * - Doesn't actually do the sending part, called from Mail::to() call.
 * - See: TestController::test_stock_backinstock()
 */
namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;


use App\Models\Store;
use App\Models\Customer;
use App\Models\Product;



/**
 * \brief Send an email confirming that a product is now back in stock.
 * - Send a templated email to the requested email explaining that an item is now back in stock.
 */
class BackInStockEmail extends Mailable
{
use Queueable, SerializesModels;


/**
 * The store object from the database tables stores, has all the details about the seelcted store.
 * @var object $store
 */
public $store;


/**
 * The email address to send the email to.
 * @var string $email
 */
public $email;


/**
 * The selected Product
 * @va mixed $product
 */
public $product;



/**
 * The view email template to use.
 * @var string template
 */
protected $template;


/**
 * The template to use
 * @var string $ACTION
 */
private $ACTION="back_in_stock";


    /**
     * Create a new job instance initialize mail transport and save store and email details away.
     * Also fetch relevant template using ACTION tag.
	 *
     * @param 	mixed	$store	store data collection
     * @param	string	$email	email address of customer
     * @param 	mixed	$product
     * @return	void
     */
    public function __construct($store, $email, $product)
    {
		$this->store = $store;
		$this->email = $email;
		$this->product = $product;
		$this->customer = Customer::where('customer_email', $email)->first();
		$this->template = "Mail.".$this->store->store_env_code.".".$this->ACTION;
		$this->hash = "2874-".hash('ripemd160', $email.$store->store_env_code);
	}




    /**
     * Fetch the data and pass into the view
	 *
     * @return mixed
     */
    public function build()
    {
		$subject = $this->store->store_name." - Product back in stock -> ".$this->product->prod_title;
        return $this->from($this->store->store_sales_email,$this->store->store_name)->subject($subject)->view($this->template); 
    }
}
