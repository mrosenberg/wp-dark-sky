<?php

/**
 * A simple wrapper for the Dark Sky API.
 * More information about the Dark Sky API can be found here:
 *
 *      https://developer.darkskyapp.com/docs
 *
 * @package DarkSky
 * @author  Bill Israel <bill.israel@gmail.com>
 * @license MIT
 */
class DarkSky
{
    /** @var BASE_URL The base url for the API calls */
    const BASE_URL = 'https://api.forecast.io';

    /** @var string $apiKey The Dark Sky developer API key */
    private $apiKey;

    /** @var array $options An array of possible options */
    private $options = array(
        'suppress_errors' => false
    );

    /** @var array $arrContextOptions An array of SSL options */
    private $arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	);  

    /**
     * Constructor.
     *
     * @param string $apiKey  The developer's API key
     * @param array  $options An array of options
     */
    public function __construct($apiKey, $options = array())
    {
        $this->apiKey = $apiKey;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Retrieves the forecast for the given latitude and longitude.
     *
     * @param $lat  float The latitude
     * @param $long float The longitude
     *
     * @return array The decoded JSON response from the API call
     */
    public function getForecast($lat, $long)
    {
        $endpoint = sprintf('/forecast/%s/%s,%s', $this->apiKey, $lat, $long);
        return $this->makeAPIRequest($endpoint);
    }


    /**
     * Retrieves the conditions for the given latitude and longitude.
     *
     * @param $lat  float The latitude
     * @param $long float The longitude
     *
     * @return array The decoded JSON response from the API call
     */
    public function getConditions($lat, $long)
    {
        $endpoint = sprintf('/forecast/%s/%s,%s,%s', $this->apiKey, $lat, $long, time());
        return $this->makeAPIRequest($endpoint);
    }    

    /**
     * Makes a request to the Dark Sky API. Does *not* use the cURL library; however,
     * it does require the server to have allow_url_fopen enabled.
     *
     * @param $url string The URL endpoint to hit
     *
     * @return array The decoded JSON response from the API call
     *
     * @throws \Exception If we can't contact the API or
     *                    the API call returns a response that can't be decoded
     */
    private function makeAPIRequest($endpoint)
    {

        $url = self::BASE_URL . $endpoint;

        if ($this->options['suppress_errors']) {
            $response = wp_remote_get($url);
        } else {
            $response = wp_remote_get($url);
        }

        if ($response === false) {
            throw new \Exception('There was an error contacting the DarkSky API.');
        }

        $json = json_decode($response['body'], true);

        if ($json === null) {
            switch($error_code = json_last_error()) {
                case JSON_ERROR_SYNTAX:
                    $reason = 'Bad JSON Syntax';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $reason = 'Unexpected control character found';
                    break;
                default:
                    $reason = sprintf('Unknown error. Error code %s', $error_code);
                    break;
            }

            throw new \Exception(sprintf('Unable to decode JSON response: %s', $reason));
        }

        return $json;
    }
}