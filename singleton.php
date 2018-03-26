<?php

final class Singleton {

    private static $instance;

    # Private constructor to ensure it won't be initialized
    private function __construct(){}

    /**
     * Returns the instance of this object
     * @return object Will only return one instance
     */
    public static function init(){
        if(!isset(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }
}

?>