<?php
/**
 * \class	EmailUserJob
 * \author	Sid Young <sid@off-grid-engineering.com>
 * \date	2016-10-19
 * \version	1.0.0
 * 
 *
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
 * \addtogroup Internal
 * EmailUserJob - Common routine to Email a user.
 */
namespace App\Jobs;


use App\Jobs\Job;


use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;


use App\Traits\Logger;


/**
 * \brief Common Job to send an email given the TO, FROM, SUBJECT and BODY - Uses Swift Mailer Class
 *
 * Send a pre-formatted email to an address using Swift Mailer Transport. 
 */
class EmailUserJob extends Job
{
use Logger;


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
		$this->setFileName("larvela-emails");
		$this->setClassName("larvela-emails");
		$this->to = $to;
		$this->from = $from;
		$this->subject = $subject;
		$this->body = $body;
		if(is_array($from))
			$this->LogMsg( "TO [".$to."]  FROM [".print_r($from,1)."] - ".$subject );
		else
			$this->LogMsg( "TO [".$to."]  FROM [".$from."] - ".$subject );

		#
		# 2018-10-08 Replaced with NEW
		#$this->transport = Swift_SmtpTransport::newInstance('localhost', 25);
		$this->transport = new Swift_SmtpTransport('localhost', 25);
		$this->mailer =  new Swift_Mailer($this->transport);
    }




    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$message =  new Swift_Message($this->subject);
		$message->setTo($this->to);
		$message->setFrom($this->from);
		$message->setBody($this->body, 'text/html');
		$this->mailer->send($message);
		$this->LogMsg( "Email sent.");
    }
}
