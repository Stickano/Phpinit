<?php

final class SessionsHandler {

    # Private constructor to ensure it won't be initialized
    private function __construct(){}

    # Return the instance
    private static $instance;
    public static function init(){
        if(!isset(self::$instance))
            self::$instance = new self();

        self::startSessions();
        return self::$instance;
    }

    /**
     * (Re)starts sessions
     * @return null starts the session
     */
    private static function startSessions(){
        if(session_status() != true)
            session_start();
    }

    /**
     * Defines a new session
     * @param string $name  The name of the new session
     * @param string $value The value of the new session
     */
    public function set($name, $value){
        $_SESSION[$name] = $value;
    }

    /**
     * Returns the value of a session
     * @param  string $name The name of the session 
     * @return string       The value of the session
     */
    public function get($name){
        if(isset($_SESSION[$name]))
            return $_SESSION[$name];
    }

    /**
     * Checks if a session is set
     * @param  string $name The name of a session
     * @return bool         TRUE/FALSE 
     */
    public function isset($name){
        return isset($_SESSION[$name]);
    }

    /**
     * Unsets a session
     * @param  string $name The name of the session
     * @return null         Unsets the session
     */
    public function unset($name){
        if(isset($_SESSION[$name]))
            unset($_SESSION[$name]);
    }

    /**
     * Destroy all sessions
     * @return null destroys sessions
     */
    public function destroy(){
        session_destroy();
    }
}

?>