<?php

class Time {

	private $zone;
	private $stamp;

    /**
     * Constructor
     * @param string $zone  Timezone
     * @param string $stamp Return format
     */
	public function __construct($zone = "Europe/Copenhagen", $stamp = "d-m-Y H:i"){
		$this->zone = $zone;
		$this->stamp = $stamp;

        date_default_timezone_set($this->zone);
	}

	/**
	 * Create a timestamp
	 * @return string date according to $stamp
	 */
	public function timestamp($val = null){
        $date = date($this->stamp);

        if($val == "day")
            return substr($date, 0, 2);
        if($val == "month")
            return substr($date, 3, 2);
        if($val == "year")
            return substr($date, 6, 4);
        if($val == "time")
            return substr($date, -5);

        return $date;
    }

	/**
     * Return a greeting message, depending on the time of day.
     * @return string Good [morning/day/afternoon/evening/night]
     */
    public function greeting(){
        $stamp = self::timestamp();
        $pos = strpos($stamp, ':')-2;
    	$tod = substr($stamp, $pos, 2);
    	if($tod >= "05" && $tod <= "09")
    		$mess = "Good morning";
    	elseif($tod >= "10" && $tod <= "13")
    		$mess = "Good day";
    	elseif($tod >= "14" && $tod <= "18")
    		$mess = "Good afternoon";
    	elseif($tod >= "19" && $tod <= "22")
    		$mess = "Good evening";
    	else
    		$mess = "Goodnight";
    	return $mess;
    }
}

?>