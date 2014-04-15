<?php

namespace badmushroom\strava;

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
abstract class Base
{
    /**
      * Version
      */
    const VERSION = '0.2.0';

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

            // Set App Config
            $this->setConfigVariables($config);

            // Base OAth URL
            $this->oauthUrl = 'https://www.strava.com/oauth/';

            // Base API URL
            $this->apiUrl = 'https://www.strava.com/api/v3';

            // Response Type is always 'code'
            $this->responseType = 'code';

        } else {
            throw new \Exception("Error: __construct() - Configuration array is missing.");
        }
    }

    /**
      * Set Config Variables
      *
      * Adds config items as properties
      *
      * @param array $config
      * @return void
      */
    public function setConfigVariables($config)
    {
        foreach ($config as $variable => $value) {
            $this->$variable = $value;
        }

        return;
    }
}
