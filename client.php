<?php

class Client {

	private $default;
	private $multi;

	/**
	 * Constructor
	 * @param array  $multi   Available (host) languages
	 * @param string $default The default (host) language
	 */
	public function __construct($multi=array(), $default='en') {
		$this->default = $default;
		$this->multi   = $multi;
	}

	/**
	 * Return the clients browser language.
	 * May be used to set a default language for the client.
	 * @return string   Clients language/ according to availables
	 */
	public function lang(){

		$lang = $this->default;

		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			$lang = substr($lang, 0, 2);

			# Return default language, if included in ISO array
			if(!empty($this->multi)){
				foreach ($this->multi as $iso) {
					if($iso == $lang)
						return $iso;
				}
			}
			return $lang;
		}
		return $lang;
	}

	/**
     * Will try and grap the client IP-address
     * Thx to https://stackoverflow.com/a/15699240
     * @return string The IP-address
     */
	public function ip() {
        $ipaddress = 'Unknown';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        return $ipaddress;
    }

    /**
     * Returns the current URL address
     * @return string URL
     */
    public function getUrl(){
        $qs = null;
        if(!empty($_SERVER['QUERY_STRING']))
            $qs = "?".$_SERVER['QUERY_STRING'];
        $url = $_SERVER['PHP_SELF'].$qs;
        return $url;
    }
}

?>