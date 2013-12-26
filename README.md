# Strava PHP V3 API #

---

## About ##

A php wrapper for Strava's API (version 3).

[Strava](http://strava.com "Strava")

## Requirements ##

- PHP 5.3.0 or higher
- cURL Library
- JSON Library
- Registered Strava App/API Key (http://www.strava.com/developers)

## Getting started ##

You'll need to [register] (http://www.strava.com/developers) your app with strava first. In exchange you'll get
an OAuth Client ID, Secret Key, Access, Key, etc.

### Initialize the class ###

```php
<?php
    require '/<path>/stravaV3/strava.php';

    $strava = new \stravaV3\Strava(array(
        'accessToken'  => '<YOUR ACCESS TOKEN>',
        'secretToken' => '<YOUR SECRET TOKEN>',
        'clientID' => <YOUR CLIENT ID>,
        'redirectUri' => 'http://example.com/strava', // You can use http://localhost during testing
        'cacheDir' => '<YOUR PATH TO CACHE DIRECTORY'>, // Must be writable by your web server
        'cacheTtl' => 900 // Numbder of seconds the cache file is good for (900 = 15 minutes).
    ));

?>
```

### Authenticate (OAuth2) ###

```php
<?php
    $strava->requestAccess();
?>
```

See example.php for a full working example.


## Methods ##

The only method (at this time) that you'll call is the makeApiCall() method.

```php
<?php
    $athlete = $strava->makeApiCall('athlete');
    var_dump($athlete);
?>
```

makeApiCall() takes three arguments argument: Function, Method, Parameters.

* Function (required): the type of information to return. [See API Docs](http://strava.github.io/api/ "Strava API Documentation")
* Parameters (optional): An array of additional parameters [See API Docs](http://strava.github.io/api/ "Strava API Documentation")
* Method (optional): get or post (delete, put, head are not yet supported). Default is 'get'.

All data is returned in JSON format. Since we're limited to the number of calls we can make in a 15 minute period,
makeApiCall() will automatically look for a cached version of the data before contacting Strava. If a valid cache
doesn't exist the method will save the response from Strava as a serialized, JSON decoded string
to be used in subsequent requests.

## History ##

**StravaV3 0.2.1 - 2013-12-25**
- PSR-0

**StravaV3 0.2.0 - 2013-11-30**
- Added response caching
- Added check for 401 header
- Fixed bug that caused $api_url to be malformed when passing a parameters array

**StravaV3 0.1.0 - 2013-11-28**
- Initial release

## Credits ##

Copyright (c) 2013 - Chris Sprague
Released under the [GPL License](http://www.gnu.org/licenses/gpl-3.0.txt).