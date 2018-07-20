<?php

class Sanitizer {

	private $find;

	/**
	 * Constructor
	 * @param array $words [Optional] Additional words to remove
	 */
	public function __construct(array $words = array()){
		$this->find = array(
			'@<script[^>]*?>.*?</script>@si',
    		'@<[\/\!]*?[^<>]*?>@si',
    		'@<style[^>]*?>.*?</style>@siU',
    		'@<![\s\S]*?--[ \t\n\r]*>@'
    	);

    	if(!empty($words))
    		array_merge($this->find, $words);
	}

	/**
	 * Sanitize (clean) a string from tags and the like
	 * @param  string $txt The value to sanitize
	 * @return string      The sanitized value
	 */
	public function sanitize(string $txt) {
		$clean = preg_replace($this->find, '', $txt);
		$clean = str_replace('\'', '', $clean);
		$clean = str_replace('"', '', $clean);
		return strip_tags($clean);
	}
}

?>