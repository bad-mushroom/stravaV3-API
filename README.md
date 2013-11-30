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
        'access_token'  => '<YOUR ACCESS TOKEN>',
        'secret_token' => '<YOUR SECRET TOKEN>',
        'client_id' => <YOUR CLIENT ID>,
        'redirect_uri' => 'http://example.com/strava' // You can use http://localhost during testing
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
* Method (optional): get or post (delete, put, head are not yet supported). Default is 'get'.
* Parameters (optional): An array of additional parameters [See API Docs](http://strava.github.io/api/ "Strava API Documentation")

All data is returned in JSON.

## History ##

**StravaV3 0.1.0 - 2013-11-28**
- Initial release

## Credits ##

Copyright (c) 2013 - Chris Sprague
Released under the [GPL License](http://www.gnu.org/licenses/gpl-3.0.txt).