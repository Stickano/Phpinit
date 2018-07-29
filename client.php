<?php

class Client {

	public function __construct() {}

	/**
	 * Return the clients browser language.
	 * May be used to set a default language for the client.
	 * @return string   Clients language/ according to availables
	 */
	public function lang(array $multi=array(), string $default='en'){

		$lang = $default;

        if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            return $lang;

		$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$lang = substr($lang, 0, 2);

		# Return default language, if included in ISO array
		if(!empty($multi)){
			foreach ($multi as $iso) {
				if($iso == $lang)
					return $iso;
			}
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
     * Return either the current or previous URL
     * @param  bool|boolean $previous True/False if you want previous URL
     * @return string                 The URL
     */
    public function getUrl(bool $previous=false){
        if ($previous && isset($_SERVER['HTTP_REFERER']))
            return $_SERVER['HTTP_REFERER'];

        $qs = null;
        if(!empty($_SERVER['QUERY_STRING']))
            $qs = "?".$_SERVER['QUERY_STRING'];
        $url = $_SERVER['PHP_SELF'].$qs;
        return $url;
    }

    /**
     * Gather client information.
     * This only gathers a few key values; Browser, platform & device
     *
     * This method requires you to download
     * the browscap file: http://browscap.org/
     * and define its location in the php.ini file.
     * Since we are only requesting a few data values,
     * the lite browscap file is sufficient.
     * @return array   Browser, system & device information
     */
    public function browscap() {
        $browser = get_browser(null, true);
        $data    = array();

        $data['browser'] = $browser['browser']." ".$browser['version'];
        $data['system']  = $browser['platform'];
        $data['device']  = $browser['device_type'];

        return $data;
    }
}

?>