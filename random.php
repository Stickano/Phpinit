<?php

class Random {

	private $string;
	private $length;

	/**
	 * Constructor
	 * @param string/int $string What characters to randomize
	 */
	public function __construct($string = null) {
		if($string == null){
			$this->string  = "abcdefghijklmnopqrstuvwxyz";
			$this->string .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$this->string .= "0123456789";
		}else{
			$this->string = $string;
		}

		$this->length = strlen($this->string)-1;
	}

	/**
	 * Generates a random string
	 * @param  integer $length How long the string should be
	 * @return string          The random generated string
	 */
	public function random(int $length = 8) {

		$str = "";
		for($i = 0; $i < $length; $i++){
			$pos = rand(0, $this->length);
			$str .= $this->string{$pos};
		}
		return $str;
	}

}

?>