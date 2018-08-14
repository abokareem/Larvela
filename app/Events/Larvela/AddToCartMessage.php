<?php
/**
 * \class	AddToCartMessage
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-08-12
 *
 *
 * \addtogroup Messaging
 * AddToCartMessage - Craft a JSON string and return.
 */
namespace App\Events\Larvela;



use App\Events\Larvela\MessageTemplate;


/**
 * \brief Formulate a JSON message containing data about what has been added to a cart in which store
 */
class AddToCartMessage extends MessageTemplate
{
	public function __construct($store,$cart,$user,$product)
	{
		$data = array();
		$this->msg = json_encode($data);
	}


	/**
	 *
	 *
	 */
	protected function processMsg()
	{
		#
		# @todo Add code to process the message from the 
		#
		return $this->msg;
	}
}
