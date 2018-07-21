<?php
/**
 * \class	MailOut
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2018-04-04
 *
 * Craft and call a templated email to send to a user
 *
 */
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailOut extends Mailable
{
use Queueable, SerializesModels;

/**
 * the current store
 * @var mixed $store
 */
public $store;

/**
 * has for unsubscribe in footer
 * @var string $hash
 */
public $hash;

/**
 * the email to send to
 * @var string $email
 */
public $email;


/**
 * the email template to use - not passed into VIEW
 * @var string $template name
 */
protected $template;

public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($store, $email, $subject, $template, $hash)
    {
		$this->store = $store;
		$this->email = $email;
		$this->subject = $subject;
		$this->template = $template;
		$this->hash = $hash;
    }



    /**
     * Build the message.
     * Template is in resources/views/Mail/<Store_code>/<template>.blade.php
     * @return $this
     */
    public function build()
    {
		$store_code = $this->store->store_env_code;
		$store_email = $this->store->store_sales_email;

		$mail_template_view = "Mail.".$store_code.".".$this->template;
        return $this->from($store_email)->subject($this->subject)->view($mail_template_view);
    }
}
