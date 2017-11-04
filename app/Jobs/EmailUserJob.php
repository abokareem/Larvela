<?php
/**
 * \class	EmailUserJob
 * @author	Sid Young <sid@off-grid-engineering.com>
 * @date	2016-10-19
 *
 * [CC]
 *
 * \addtogroup Internal
 * EmailUserJob - Common routine to Email a user.
 */
namespace App\Jobs;


use App\Jobs\Job;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * \brief Common Job to send an email given the TO, FROM, SUBJECT and BODY - Uses Swift Mailer Class
 *
 * Send a pre-formatted email to an address using Swift Mailer Transport. 
 */
class EmailUserJob extends Job
{


/**
 * Email address to send to. Cannot be blank
 * @var string $to
 */
protected $to;


/**
 * Email address to send from.
 * @var string $from
 */
protected $from ="do-no-reply@online-sales-shop.domain";


/**
 * The email subject line - should not be blank
 * @var string $subject
 */
protected $subject = "no specified";


/**
 * The email message to send.
 * @var string $body
 */
protected $body;


/**
 * The swift mailer transporter.
 * @var mixed $transport
 */
protected $transport;


/**
 * The swift ailer mailer.
 * @var mixed $mailer
 */
protected $mailer;


    /**
     * Create a new job instance.
     *
     *
     * @param	string	$to		email address to send to
     * @param 	string	$from		The from address
     * @param 	string	$subject	subject line text
     * @param	string	$body		the email text body to send
     * @return	void
     */
    public function __construct($to, $from, $subject, $body)
    {
		$this->to = $to;
		$this->from = $from;
		$this->subject = $subject;
		$this->body = $body;

		$this->transport = Swift_SmtpTransport::newInstance('localhost', 25);
		$this->mailer = Swift_Mailer::newInstance($this->transport);
    }




    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$message = Swift_Message::newInstance($this->subject);
		$message->setTo($this->to);
		$message->setFrom($this->from);
		$message->setBody($this->body, 'text/html');
		$this->mailer->send($message);
#
# Capture all emails
# test code, jobs now coded to send to store owner ID=1
#
#		$message->setTo("sid.young@gmail.com");
#		$this->mailer->send($message);
    }
}
