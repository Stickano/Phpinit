<?php

class Security {

	private $host;
	private $self;
	private $qs;

    /**
     * Constructor
     */
	public function __construct() {
		$this->host = $_SERVER['HTTP_HOST'];
		$this->self = $_SERVER['PHP_SELF'];

		$this->qs = NULL;
        if(!empty($_SERVER['QUERY_STRING']))
            $qs = "?".$_SERVER['QUERY_STRING'];
	}


	/**
     * Makes sure a HTTPS connection is made
     */
    public function SecureConnect() {
    	if(!isset($_SERVER['HTTPS']) && $_SERVER['SERVER_NAME'] != 'localhost'){
            echo'<script>window.location = "https://'.$this->host.$this->self.$this->qs.'";</script>';
            exit;
        }
    }
}

?>