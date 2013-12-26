<?php

require '/<path>/Strava.php';

try {

    /**
      * The constructor expects an array of your app's Access Token, Sectret Token, Client ID, the Redirect URL, and cache directory.
      * See http://strava.github.io/api/ for more detail.
      */
    $strava = new \stravaV3\Strava(array(
        'accessToken'  => '',
        'secretToken' => '',
        'clientID' => 000,
        'redirectUri' => 'http://example.com/strava',
        'cacheDir' => '/path/to/cache/dir', // Must be writable by web server
        'cacheTtl' => 900  // Number of seconds until cache expires (900 = 15 minutes)
    ));

    // Authenticated - Strava will redirect the user to the Redicrt URL along with a 'code' _GET variable upon success
    if (isset($_GET) && isset($_GET['code'])) {

        // Send resource request key to the makeApiCall method. A JSON object will be returned.
        // What you do at this point is up to you :)
        $athlete = $strava->makeApiCall('athlete');
        var_dump($athlete);

        // Example of sending an array of parameters
        $params = array('per_page' => 3, 'page' => 5);
        $activities = $strava->makeApiCall('athlete/activities', $params);
        var_dump($activities);

    // Error
    } else if (isset($_GET) && isset($_GET['error'])) {
        echo '<strong>Error:</strong> '. $_GET['error'] .'<br />';

    // Not Authenticated - Will redirect visitor to Strava for approval
    } else {
        $strava->requestAccess();
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
