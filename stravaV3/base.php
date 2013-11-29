<?php

namespace stravaV3;

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
abstract class BaseStrava
{
    /**
      * Version
      */
    const VERSION = '0.1.0';

    /**
      * Client ID
      * @var int
      */
    protected $clientID = null;

    /**
      * Access Token
      * @var string
      */
    protected $accessToken = null;

    /**
      * Secret Token
      * @var string
      */
    protected $secretToken = null;

    /**
      * Redirect URL
      * @var string
      */
    protected $redirectUri = null;

    /**
      * Constructor
      *
      * Sets configuration
      *
      * @param array $config
      * @throws \Exception
      */
    public function __construct($config)
    {
        if (is_array($config) === true) {

            // App Config
            $this->setAccessToken($config['access_token']);
            $this->setSecretToken($config['secret_token']);
            $this->setClientID($config['client_id']);
            $this->setRedirectUri($config['redirect_uri']);
            
            // Base OAth URL
            $this->oauth_url = 'https://www.strava.com/oauth/';

            // Base API URL
            $this->api_url = 'https://www.strava.com/api/v3';

            // Response Type is always 'code'
            $this->response_type = 'code';
        } else {
            throw new \Exception("Error: __construct() - Configuration array is missing.");
        }
    }

    /**
      * Set Client ID
      *
      * @param int $clientID
      * @return void
      */
    public function setClientID($clientID)
    {
        $this->clientID = $clientID;
        return;
    }

    /**
      * Set Redirect URL
      *
      * @param string $redirectUri
      * @return void
      */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
        return;
    }

    /**
      * Set Access Token
      *
      * @param string $accessToken
      * @return void
      */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return;
    }

    /**
      * Set Secret Token
      *
      * @param string $secretToken
      * @return void
      */
    public function setSecretToken($secretToken)
    {
        $this->secretToken = $secretToken;
        return;
    }
}
