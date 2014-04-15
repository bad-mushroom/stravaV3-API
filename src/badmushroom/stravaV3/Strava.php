<?php

namespace badmushroom\stravaV3;
use badmushroom\stravaV3\Base;

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
 * @version    0.2.0
 */
class Strava extends Base
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
      * Send access request to Strava. User will need to login and (if not already) grant access to your app.
      * Once authenticated they will be redirected to the URL set in 'redirect_url'.
      *
      * @llink http://strava.github.io/api/v3/oauth/#get-authorize
      *
      * @param string $scope Space delimited string of ‘view_private’ and/or ‘write’, leave blank for read-only permissions.
      * @param string $state Returned to your application, useful if the authentication is done from various points in an app.
      * @param string $prompt Can be 'force' or 'auto'. Used to show the authorization prompt even if the user has already authorized the current application.
      */
    public function requestAccess($scope = null, $state = null, $prompt = null)
    {
        $url = $this->oathUrl . 'authorize?client_id=' . $this->clientID . '&response_type=' . $this->responseType .
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
      * @link http://strava.github.io/api/v3/oauth/#post-token
      *
      * @return string
      */
    public function getOAuthToken()
    {
        $url = $this->oathUrl . '/token';

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
        $http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check to make sure user hasn't removed access to app'
        if ($http_response_code == 401) {
            throw new \Exception('Error: getOAuthToken() - Access has been revoked.');
        }

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
    public function makeApiCall($function, $parameters = null, $method = 'get')
    {
        $this->getOAuthToken();

        if (isset($parameters) && is_array($parameters)) {
            $parameter_string = http_build_query($parameters);
        } else {
            $parameter_string = null;
        }

        $api_url = $this->apiUrl . '/' . $function . '?';
        $api_url .= ($method === 'get' && $parameter_string !== null) ? $parameter_string .'&' : null;
        $api_url .= 'access_token=' . $this->accessToken;

        // check cache
        $json_cache = $this->getCacheData($api_url);

        if (($json_cache !== false) && ($method === 'get')) {
            return $json_cache;
        }

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

        $this->writeCacheData($api_url, $json_response);

        return json_decode($json_response);
    }

    /**
      * Get Cache Data
      *
      * Returns the JSON response from cache if it exists and hasn't expired.
      *
      * @param string $apiUrl
      * @return boolean|string
      */
    private function getCacheData($apiUrl)
    {
        $cache_file = $this->getCacheName($apiUrl);
        $cache_expires = time() - $this->cacheTtl;

        if (file_exists($cache_file) && filemtime($cache_file) >= $cache_expires) {
            $cache_data = file_get_contents($cache_file);

            return unserialize($cache_data);
        }

        return false;
    }

    /**
      * Write Cahce Data
      *
      * Writes the JSON response to a cache file.
      *
      * @param string $apiUrl
      * @param string $json_response
      * @throws \Exception
      */
    private function writeCacheData($apiUrl, $json_response)
    {
        if (is_writable($this->cacheDir)) {
            $cache_file = $this->getCacheName($apiUrl);

            // Delete expired cahce file it it exists
            if (file_exists($cache_file)) {
                unlink($cache_file);
            }

            $cache_data = json_decode($json_response);
            file_put_contents($cache_file, serialize($cache_data));

        } else {
            throw new \Exception('Error: writeCacheData() - Cache driectory is not writable.');
        }

        return;
    }

    /**
      * Get Cache Name
      *
      * Returns the filename of the would be cache name.
      *
      * @param string $apiUrl
      * @return string
      */
    private function getCacheName($apiUrl)
    {
        $cache_name = md5($apiUrl) . '.cache';
        $cache_filename = $this->cacheDir . DIRECTORY_SEPARATOR . $cache_name;

        return $cache_filename;
    }
}
