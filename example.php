<?php

require '/<path>/strava.php';

try {

    /**
      * The constructor expects an array of your app's Access Token, Sectret Token, Client ID, and the Redirect URL.
      * See http://strava.github.io/api/ for more detail.
      */
    $strava = new \stravaV3\Strava(array(
        'access_token'  => '',
        'secret_token' => '',
        'client_id' => 000,
        'redirect_uri' => 'http://example.com/strava'
    ));

    // Authenticated - Strava will redirect the user to the Redicrt URL along with a 'code' _GET variable upon success
    if (isset($_GET) && isset($_GET['code'])) {

        // Send resource request key to the makeApiCall method. A JSON decoded string will be returned.
        // What you do at this point is up to you :)
        $athlete = $strava->makeApiCall('athlete');
        var_dump($athlete);

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
