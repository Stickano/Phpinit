<?php

class Base64 {

	public function decode($string) {
		$base = strtr($string,  '-_~', '+/=');
		return base64_decode($base);
	}

	public function encode($string) {
		$base = base64_encode($string);
		return strtr($base, '+/=', '-_~');
	}
}

?>