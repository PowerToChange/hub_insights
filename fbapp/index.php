<?php

require 'src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '283493411818583',
  'secret' => '31ef464668c8d812ee5de9ed6eeb83ae',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
  }
}
else {
  $loginUrl = $facebook->getLoginUrl(array(
    'scope' => 'email',
    'redirect_uri' => 'https://apps.facebook.com/powertochangeq/',
  ));

  print('<script> top.location.href=\'' . $loginUrl . '\'</script>');
  exit();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <script src="//code.jquery.com/jquery.js"></script>
  </head>
  <body>
    <?php if ($user) { ?>
      Your user profile is
      <pre>
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre>
    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>
    <a href="#" id="share">Share</a>
    <div id="fb-root"></div>
    <script type="text/javascript">
    $(document).ready(function() {
      $.ajaxSetup({ cache: true });
      $.getScript('//connect.facebook.net/en_US/all.js', function(){
        FB.init({
          appId: '283493411818583',
        });     
        $('#loginbutton,#feedbutton').removeAttr('disabled');
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            // the user is logged in and has authenticated your
            // app, and response.authResponse supplies
            // the user's ID, a valid access token, a signed
            // request, and the time the access token 
            // and signed request each expire
            var uid = response.authResponse.userID;
            var accessToken = response.authResponse.accessToken;
          } else if (response.status === 'not_authorized') {
            // the user is logged in to Facebook, 
            // but has not authenticated your app
          } else {
            // the user isn't logged in to Facebook.
          }
        });
      });
      $("#share").click(function(){
        FB.ui({method: 'apprequests',
          title: 'Play P2C Perspectives with me!',
          message: 'P2C Perspectives is perplexing! Check it out.',
        }, function(response) {
          if (response && response.post_id) {
            alert('Post was published.');
          } else {
            alert('Post was not published.');
          }
        });
      });
    });
    </script>
  </body>
</html>
