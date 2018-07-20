<?php

class Mailer {

	private $reciever;
	private $serverMail;

	private $confirmMail;
	private $confirmSub;
	private $confirmBody;

	private $noValidMail;
	private $missingFields;

	/**
	 * Constructor
	 * @param string $reciever A valid Email
	 */
	public function __construct($reciever) {
		if(!filter_var($reciever, FILTER_VALIDATE_EMAIL))
			throw new Exception('Not a valid Email: ' . $reciever);
		$this->reciever = $reciever;
		$this->serverMail = $this->reciever;
		$this->confirmMail = false;

		$this->noValidMail = "Fill out all inputs.";
		$this->missingFields = "Use a valid Email.";
	}

	/**
	 * Define the return message if it's an elligible Email addr
	 * @param string $string The return
	 */
	public function setIllegibleMail($string) {
		$this->noValidMail = $string;
	}

	/**
	 * Define the return message subject/body is empty
	 * @param [type] $string [description]
	 */
	public function setMissingFields($string) {
		$this->missingFields = $string;
	}

	/**
	 * Returns a mail to sender
	 * @param  string $subject
	 * @param  string $message
	 */
	public function confirmMail($subject, $message) {
		$this->confirmMail = true;
		$this->confirmSub = $subject;
		$this->confirmBody = $message;
	}

	/**
	 * Reset the MailSuccess session
	 * @return  Unsets session
	 */
	public function resetMail(){
		if(isset($_SESSION['MailSuccess']))
			unset($_SESSION['MailSuccess']);
	}

	/**
	 * Define the server mail
	 * Default is the reciever address
	 * @param string $string A valid email
	 *                       Should match your server,
	 *                       ex noreply@MyServer.com
	 */
	public function setServerMail($string) {
		if(!filter_var($string, FILTER_VALIDATE_EMAIL))
			throw new Exception('Not a valid Email: ' . $string);
		$this->serverMail = $string;
	}

	/**
	 * Send the mail to reciever (defined in constructor)
	 * @param  string $sender  A valid E-mail address
	 * @param  string $subject A valid subject
	 * @param  string $message A valid message body
	 *
	 * Returns one of the following SESSIONS
	 * @return SESSION         $_SESSION['MailSuccess'] = true;
	 * @return SESSION         $_SESSION['MailFailed'] = {reason}
	 */
	public function mail($sender, $subject, $message) {

		$eRR = NULL;

		# Sanitize the inputs
		$sender = filter_var($sender, FILTER_SANITIZE_EMAIL);
		$subject = filter_var($subject, FILTER_SANITIZE_STRING);
		$message = filter_var($message, FILTER_SANITIZE_STRING);

		# Confirm that it is a valid Email Address
		if(!filter_var($sender, FILTER_VALIDATE_EMAIL) && !empty($sender))
			$eRR = $this->noValidMail." ";

		# Confirm that all inputs has been filled
		if(empty($subject) || empty($message))
			$eRR .= $this->missingFields;

		# Break and send back, with a message, if an error occurred
		if($eRR != NULL)
			throw new Exception($eRR);

		# If the error handler didn't break - send the Email
		$to = $this->reciever;
		$from = $this->serverMail;
		$subject = $subject;
		$headers = 'From: '.$from.'\r\n' .
					'Content-type: text/plain; charset=utf-8'.'\r\n';
		$message = 'Reply to this Email: '.$sender.'\n'.nl2br($message);
		mail($to, $subject, $message, $headers);

		# If chosen to send a confirm mail
		if($this->confirmMail == true){
			$to = $sender;
			$from = $this->serverMail;
			$subject = $this->confirmSub;
			$headers = 'From: '.$from.'\r\n' .
						'Content-type: text/plain; charset=utf-8'.'\r\n';
			$message = nl2br($this->confirmBody);
			mail($to, $subject, $message, $headers);
		}

		$_SESSION['MailSuccess'] = true;
	}
}

?>