<?php

class Crypto {

	/**
	 * For the Encrypt/Decrypt functions
	 *	Output Encoding (format)
	 *	 0: RAW
	 *	 1: B64
	 *	 2: HEX
	 */
	private $format;
	private $cipher;
	private $hash;

	/**
	 * Constructor
	 * @param string $cipher A valid (prolly) AES cipher
	 * @param string $hash   A valid hashing method
	 * @param string $format 'raw', 'b64' or 'hex'
	 */
	public function __construct($cipher='aes-256-ctr', $hash ='sha256', $format ='b64'){
		
		$format = strtolower($format);
		if($format == 'raw')
			$this->format = 0;
		elseif($format == 'hex')
			$this->format = 2;
		else
			$this->format = 1;

		$this->cipher = $cipher;
		$this->hash = $hash;
	}

	public function __toString(){
		return  "[Format: ".$this->format."] 
				[Cipher: ".$this->cipher."]
				[Hash: ".$this->hash."]";
	}


	/**
	 * Returns available cipher methods
	 */
	public function GetCiphers() {
		foreach (openssl_get_cipher_methods() as $cipher) {
			echo $cipher.'<br>';
		}
	}
	
	/**
	 * Returns available hashing algorithms
	 */
	public function GetHashes() {
		foreach (openssl_get_md_methods() as $hash) {
			echo $hash.'<br>';
		}
	}


	/**
	 * This function will Encrypt your data
	 * @param  string $string Data to be encrypted
	 * @param  string $key    Encryption key
	 * @return string         The encrypted string
	 */
	public function encrypt($string, $key) {

		# Confirm the Cipher and Hash methods are available
		if(!in_array($this->cipher, openssl_get_cipher_methods(true)))
			return "Unknown cipher! Available ciphers are;<br>".GetCiphers();

		if(!in_array($this->hash, openssl_get_md_methods(true)))
			return "Unknown hash! Available hashing methods are;<br>".GetHashes();

		# Random Initialization Vector
		$length = openssl_cipher_iv_length($this->cipher);
		if(function_exists('random_bytes'))
			$bytes = random_bytes($length);
		else
			$bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

		# Hash the key
		$keyhash = openssl_digest($key, $this->hash, true);

		# Encrypt
		$encrypt = openssl_encrypt($string, $this->cipher, $keyhash, OPENSSL_RAW_DATA, $bytes);

		# Confirm the action completed
		if($encrypt == false)
			return "Something went wrong: ".openssl_error_string();

		$result = $bytes.$encrypt;

		# Format the result
		if($this->format == 1)
			$result = base64_encode($result);
		elseif($this->format == 2)
			$result = unpack('H*', $result)[1];

		return $result;
	}


	/**
	 * This function will Decrypt your data
	 * @param  string $string The encoded message to decrypt
	 * @param  string $key    The key to Decrypt the message with
	 * @return string         Returns the decrypted message
	 */
	public function decrypt($string, $key) {

		# Format
		if($this->format == 1)
			$string = base64_decode($string);
		if($this->format == 2)
			$string = pack('H*', $string);

		# Integrity check
		$length = openssl_cipher_iv_length($this->cipher);
		if(strlen($string) < $length)
			return "Data length ".strlen($string)." is less than byte (iv) length ".$length;

		# Extract the Initialization Vector
		$byte = substr($string, 0, $length);
		$string = substr($string, $length);

		# Hash the key
		$keyhash = openssl_digest($key, $this->hash, true);
		
		# Decrypt
		$decrypt = openssl_decrypt($string, $this->cipher, $keyhash, OPENSSL_RAW_DATA, $byte);

		# Confirm the action completed
		if($decrypt == false)
			return "Decryption failed: ".openssl_error_string();

		return $decrypt;
	}
}

?>