<?php

namespace stravaV3;

require_once 'base.php';

/**
 * Strava API V3 php Wrapper
 *
 * Acts as an interface to Strava's V3 API service. http://http://strava.github.io/api/
 *
 * Requires php 5.3.0+, JSON, and cURL libraries.
 *
 * License:  StravaV3 php is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * StravaV3 phpis distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * @author     Chris Sprague <chris@chaoscontrol.org>
 * @copyright  2013 Chris Sprague
 * @license    http://www.gnu.org/licenses/gpl.html GNU Public License
 * @version    0.1.0
 */
class Strava extends \stravaV3\BaseStrava
{
    /**
      * Constructor
      *
      * @param array $config
      */
    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
      * Request Access
      *
      * Send access request to Strava.
      *
      * @param string $scope
      * @param string $state
      * @param string $prompt
      */
    public function requestAccess($scope = null, $state = 'app', $prompt = null)
    {
        $url = $this->oauth_url . 'authorize?client_id=' . $this->clientID . '&response_type=' . $this->response_type .
            '&redirect_uri=' . urlencode($this->redirectUri);

        if ($scope != null) {
            $url .= '&scope=' . urlencode($scope);
        }

        if ($state != null) {
            $url .= '&state=' . urlencode($state);
        }

        if ($prompt != null) {
            $url .= '&approval_prompt=' . urlencode($prompt);
        }

        header('location: ' . $url);
        exit;
    }

    /**
      * Get OAuth Token
      *
      * Performs a token exchange with Strava.
      *
      * @return string
      */
    public function getOAuthToken()
    {
        $url = $this->oauth_url . '/token';

        // Post fields
        $fields = array(
            'client_id' => $this->clientID,
            'client_secret' => $this->secretToken,
            'code' => $_GET['code']
        );

        $parameters = '&' . http_build_query($fields);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($json_response);
    }

    /**
      * Make API Call
      *
      * Sends a request to Strava and decodes the JSON result.
      *
      * @param string $function
      * @param string $method
      * @param array $parameters
      * @return string
      */
    public function makeApiCall($function, $method = 'get', $parameters = null)
    {
        // @todo check validity
        $token = $this->getOAuthToken();

        if (isset($parameters) && is_array($parameters)) {
            $parameter_string = '&' . http_build_query($parameters);
        } else {
            $parameter_string = null;
        }

        $api_url = $this->api_url . '/' . $function . '?access_token=' . $this->accessToken;
        $api_url .= ($method === 'get') ? $parameter_string : null;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'post') {
            curl_setopt($ch, CURLOPT_POST, count($parameters));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }

        $json_response = curl_exec($ch);
        curl_close($ch);

        return json_decode($json_response);
    }
}