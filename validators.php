<?php
	
class Validators {

	/**
	 * Validate Emails
	 * @param  string $input Email to validate
	 * @return bool        true/false
	 */
	function valMail($input) {
		if(!filter_var($input, FILTER_VALIDATE_EMAIL))
			return false;
		return true;
	}

	/**
	 * Validate Integers
	 * @param  int $input Integer to validate
	 * @return bool        true/false
	 */
	function valInt($input) {
		if (filter_var($input, FILTER_VALIDATE_INT) || filter_var($input, FILTER_VALIDATE_INT) === 0)
			return true;
		return false;
	}

	/**
	 * Validate URLs
	 * @param  string $input URL to validate
	 * @return bool        true/false
	 */
	function valUrl($input) {
		if(!filter_var($input, FILTER_VALIDATE_URL))
			return false;
		return true;
	}

	/**
	 * Validate IPs
	 * @param  string $input IP to validate
	 * @return bool        true/false
	 */
	function valIp($input) {
		if(!filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
			return false;
		return true;
	}
}

?>