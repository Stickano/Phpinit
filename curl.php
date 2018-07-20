<?php

class Curl {

    private $showError;
    private $json;
    private $data;
    private $request;

    public function __construct(){
        $this->showError = false;
        $this->json = true;
        $this->data = array();
        $this->request = "GET";
    }

    /**
     * If error should be shown upon request failure
     * @param  bool|boolean $display True/False
     * @return                       Sets the $showError variable
     */
    public function showError(bool $display = false){
        $this->showError = $display;
    }

    /**
     * If the data should be treated as JSON
     * @param  bool|boolean $json True/False
     * @return                    Sets the $json variable
     */
    public function jsonDecode(bool $json = true){
        $this->json = $json;
    }

    /**
     * If you're doing a POST requests
     * @param  array  $data POST values
     * @return              Sets the $data variable and holds the data for execution
     */
    public function post(array $data){
        $data = json_encode($data);
        $this->data = $data;
        $this->request = "POST";
    }

    /**
     * If you're doing PUT requests
     * @param  array  $data PUT values
     * @return             Sets the $data variable and holds the data for execution
     */
    public function put(array $data){
        $data = json_encode($data);
        $this->data = $data;
        $this->request = "PUT";
    }

    /**
     * Performs the query
     * @param  string $url The URL to request/post to
     * @return string|array The responds
     */
    public function curl(string $url) {
        $init = curl_init($url);
        curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($init, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($init, CURLOPT_TIMEOUT, 10);

        if($this->showError == true)
            curl_setopt($init, CURLOPT_FAILONERROR, true);

        if($this->data != null && !empty($this->data)){
            curl_setopt($init, CURLOPT_CUSTOMREQUEST, $this->request);
            curl_setopt($init, CURLOPT_POSTFIELDS, $this->data);
            curl_setopt($init, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($this->data)));
        }

        $data = curl_exec($init);
        curl_close($init);

        if($this->json == true)
            $data = json_decode($data, true);

        return $data;
    }
}

?>