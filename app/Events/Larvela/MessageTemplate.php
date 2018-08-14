<?php
/**
 * \class	MsgTemplate
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-08-12
 *
 *
 * \addtogroup Messaging
 * MsgTemplate - Abstract class implementing the Template design pattern.
 */
namespace App\Events\Larvela;


/**
 * \brief An abstract template class for crafting and dispatching Business Process Messages.
 */
abstract class MsgTemplate
{
public $msg=null;

	public final function dispatch()
	{
		$this->processMsg();
		#
		# dispatch below using Job dispatch with queuing
		#
		
	}

	abstract protected function processMsg();
}
