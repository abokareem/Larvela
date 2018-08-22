<?php
/**
 * \class	OrderPlaced
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-08-22
 * \version	1.0.0
 *
 * Copyright 2018 Sid Young, Present & Future Holdings Pty Ltd
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * 
 * \addtogroup Transactional
 * OrderPlaced - Provides a hook when an Order is placed.
 * - Send a txt email to the store owner.
 * - Payment may not yet have been made.
 */
namespace App\Jobs;


use App\Jobs\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Customer;




/**
 * \brief an Order has been placed, we have a point here where we can insert additional business logic.
 * - Order will still need to be picked and dispatched.
 * - Customer email sent via a Mailable job.
 */
class OrderPlaced implements ShouldQueue
{
use InteractsWithQueue, Queueable, SerializesModels;

/**
 * The store object from the database tables stores, has all the details about the selected store.
 * @var mixed $store
 */
protected $store;


/**
 * The order object fromt eh database table "orders"
 * @var mixed $order
 */
protected $order;


/**
 * The email address to send to.
 * @var string $email
 */
protected $email;



    /**
     * Create a new job instance and save store and email details away.
	 *
     * @param  mixed	$store	The Store object.
     * @param  string	$email 	email address of customer
     * @param  mixed	$order	The Order object.
     * @return void
     */
    public function __construct($store, $email, $order)
    {
		$this->store = $store;
		$this->email = $email;
		$this->order = $order;
    }



    /**
	 * provide a point for additional business logic to be inserted.
	 * - Send an email to the store admin.
	 *
     * @return void
     */
    public function handle()
    {
		$admin_user = Customer::find(1);
		$from = $this->store->store_sales_email;
		$subject = "[LARVELA] Order #".$this->order->id." placed -> message sent to [".$this->to."]";
		$text = "Order #".$this->order->id." placed and email sent to [".$tis->email."]";
		dispatch(new EmailUserJob($admin_user->customer_email, $from, $subject, $text));
    }
}
