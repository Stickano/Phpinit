<?php

@session_start();

class OpenWeatherMap {

    private $key;
    private $cityId;
    private $curl;
    private $json;
    private $usingIcons; # TODO: Should be able to exclude this
    private $icons;
    private $storeJson;

    // http://openweathermap.org/help/city_list.txt
    public function __construct(string $key, int $cityId, Curl $curl){
        $this->key    = $key;
        $this->cityId = $cityId;
        $this->curl = $curl;
        $this->json = array();
        $this->usingIcons = false;
        $this->storeJson = false;
    }

    /**
     * Converts Kelvin to Celsius
     * @param  int    $kelvin   Kelvin value
     * @return int              The converted value
     */
    public function celsiusConverter(int $kelvin){
        return $kelvin-272.15;
    }

    /**
     * Converts Kelvin to Fahrenheit
     * @param  int    $kelvin   Kelvin value
     * @return int              The converted value
     */
    public function fahrenheitConverter(int $kelvin){
        return $kelvin-457.87;
    }

    /**
     * Makes the API call
     * @return array    Sets the $json variable with the respond
     */
    public function apiCall(){
        $time = date('YmdHi');
        if(!isset($_SESSION['OpenWeatherMapApiCall']) || ($time - $_SESSION['OpenWeatherMapApiCall']) > 10){
            $_SESSION['OpenWeatherMapApiCall'] = $time;
            $request = 'http://api.openweathermap.org/data/2.5/forecast?id='.$this->cityId.'&APPID='.$this->key;
            $this->json = $this->curl->curl($request);

            # Store the json content if chosen with storeJson()
            if($this->storeJson == true && !empty($this->json))
                file_put_contents($this->storeJson, json_encode($this->json));
        }

        # If available, load the last API call from local json
        if(is_file($this->storeJson))
            $this->json = json_decode(file_get_contents($this->storeJson), true);
    }

    /**
     * Search the array and return the interesting values
     * @param  int|integer $hours Read [Hours] into the future
     *                            Leave blank for current weather.
     * @return array             'temp', 'main', 'description', 'icon',
     *                           'windSpeed', 'windDegree'
     */
    public function getWeather(int $hours=0){
        if($hours > 3)
            $hours = floor($hours/3);

        if(is_file($this->storeJson) && isset($_SESSION['OpenWeatherMapApiCall']))
            $this->json = json_decode(file_get_contents($this->storeJson), true);

        $weather = array();
        if(!empty($this->json)){
            $weather = ['temp'        => $this->json['list'][$hours]['main']['temp'],
                        'main'        => $this->json['list'][$hours]['weather'][0]['main'],
                        'description' => $this->json['list'][$hours]['weather'][0]['description'],
                        'icon'        => $this->json['list'][$hours]['weather'][0]['icon'],
                        'windSpeed'   => $this->json['list'][$hours]['wind']['speed'],
                        'windDegree'  => $this->json['list'][$hours]['wind']['deg']];
        }

        return $weather;
    }

    /**
     * Return the latest API call (in json format)
     * Be aware, that this will only return something if an API
     * call has been made - which it is only allowed to once every 10min.
     * OR if you've stored the call via storeJson()
     * @return array    The result from the API call
     */
    public function getJson(){
        if(is_file($this->storeJson))
            $this->json = json_decode(file_get_contents($this->storeJson));

        return $this->json;
    }

    /**
     * Returns an icon, either from the owm website
     * or a local library
     * @param  string $iconId The id of the icon
     * @return string         The URL to owm icons,
     *                        or the chosen icon from usingWeatherIcons()
     */
    public function getIcon(string $iconId){
        if($this->usingIcons != true)
            return 'http://openweathermap.org/img/w/'.$iconId.'.png';
        else
            return $this->icons[$iconId];
    }

    /**
     * Returns the current given cityId
     * @return int  $cityId
     */
    public function getCityId(){
        return $this->cityId;
    }

    /**
     * Switch to using locally stored Weather Icons
     * Weather icon ids: http://openweathermap.org/weather-conditions
     * Weather icons: https://erikflowers.github.io/weather-icons/
     * @param  array|null $icons An array of '[icon-id]' => 'wi-[icon]'
     *                           (Or empty for default icons)
     * @return                   Sets $icons
     */
    public function usingWeatherIcons(array $icons=null){
        # Default new icons
        $this->icons = array('01d' => 'wi-day-sunny',
                            '02d' => 'wi-day-cloudy',
                            '03d' => 'wi-cloud',
                            '04d' => 'wi-cloudy',
                            '09d' => 'wi-showers',
                            '10d' => 'wi-day-rain',
                            '11d' => 'wi-thunderstorm',
                            '13d' => 'wi-snow',
                            '50d' => 'wi-fog',
                            '01n' => 'wi-night-clear',
                            '02n' => 'wi-night-cloudy',
                            '03n' => 'wi-cloud',
                            '04n' => 'wi-cloudy',
                            '09n' => 'wi-showers',
                            '10n' => 'wi-night-rain',
                            '11n' => 'wi-thunderstorm',
                            '13n' => 'wi-snow',
                            '50n' => 'wi-fog');

        if($icons != null && is_array($icons))
            $this->icons = $icons;

        $this->usingIcons = true;
    }

    /**
     * An option that stores the API call to a file
     * @param  string $dir The directory to save the file
     * @return             Sets the $jesonStore value (dir/filename.txt)
     */
    public function storeJson(string $dir){
        if(!empty($dir) && $dir != null && substr($dir, -1) != "/")
            $dir .= "/";
        $this->storeJson = $dir.'owmCall.txt';
    }
}

?>
