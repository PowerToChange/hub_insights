<?php

// import phpCAS lib
include_once('CAS.php');

// include site defines
include_once('cas_defines.php');

if (defined('USE_CUSTOM_SESSION_HANDLERS')) {
   include_once('sessions.php');
}

//uncomment next line to turn on debugging
//phpCAS::setDebug('./debug.log');

// initialize phpCAS
phpCAS::client($cas_version, $cas_server, $cas_port, $cas_uri, $start_session);

if (defined('USE_CUSTOM_SESSION_HANDLERS')) {
   // custom session handlers require additional functions for
   // tracking the logout ticket used by single logout out
   phpCAS::setPostAuthenticateCallback('_cas_track_logout_ticket');
   phpCAS::setSingleSignoutCallback('_cas_single_signout_callback');
}

// determine CAS server verification
switch ($cas_cert_verify) {
   case CAS_NO_VERIFY:
           phpCAS::setNoCasServerValidation();
           break;
   case CAS_VERIFY:
           phpCAS::setCasServerCert($cas_cert);
           break;
   case CAS_CA_VERIFY:
           phpCAS::setCasServerCACert($cas_cert);
           break;
}

// uncomment one of the following handleLogoutRequests calls
// handle logout requests from any server 
//phpCAS::handleLogoutRequests(false);

// handle logout requests from only The Key servers (recommended)
phpCAS::handleLogoutRequests(true, array('173.45.224.95', '173.45.225.184'));

// force CAS authentication
phpCAS::forceAuthentication();

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
if (isset($_REQUEST['logout'])) {
   phpCAS::logout();
}
?>
