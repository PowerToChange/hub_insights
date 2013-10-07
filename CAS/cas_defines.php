<?php
// uncomment to use custom session handlers
//define('USE_CUSTOM_SESSION_HANDLERS','yes');

define('CAS_NO_VERIFY','none');
define('CAS_VERIFY','verify');
define('CAS_CA_VERIFY','ca_verify');

// initialize CAS client variables for The Key
$cas_version     = CAS_VERSION_2_0;
$cas_server      = 'thekey.me';
$cas_port        = 443;
$cas_uri         = '/cas';
$cas_cert_verify = CAS_NO_VERIFY;
$cas_cert        = '/etc/pki/tls/certs/ca-bundle.crt';

if (defined('USE_CUSTOM_SESSION_HANDLERS')) {
   $start_session = false;
   include_once('cas_sessions.php');
} else {
   $start_session = true;
}
?>