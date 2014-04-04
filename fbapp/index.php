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
    'scope' => 'email,publish_actions',
    'redirect_uri' => 'https://apps.facebook.com/powertochangeq/',
  ));

  //print('<script> top.location.href=\'' . $loginUrl . '\'</script>');
  //exit();
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">

    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/jquery.validate.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/bootstrap-select.min.js"></script>
  </head>
  <body>
    <script type="text/javascript">
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
        $(document).ready(function() {
          $("#fbshare").click(function(){
            FB.ui({method: 'apprequests',
              title: 'Fill out P2C Perspectives!',
              message: 'P2C Perspectives is perplexing! Check it out.',
            }, function(response) {
              if (response && response.post_id) {
                alert('Post was published.');
              } else {
                alert('Post was not published.');
              }
            });
          });

          $('#rejForm').validate({
            ignore: ":hidden:not(.selectpicker)",
            rules: {
              inputQ1: {
                required: true
              },
              inputQ2: {
                required: true
              }
            },
            highlight: function(element) {
              $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
            },
            success: function(element) {
              $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
              $(element).removeClass('error').addClass('valid').addClass('error');
            },
            submitHandler: function(form) {
              $("#formView").hide();
              $("#resultView").show();
              /*$("#activityTable tbody").prepend("<tr id='loading'><td><img class='img-responsive centre' src='/images/loading.gif'></td></tr>");
              $.getJSON(
                "/discover/ajax/submitrejoice.php", 
                $(form).serialize(),  
                function(json){
                  var alert = "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' " +
                    "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Error!</strong> " + json.result + "</div>";
                  if(json.result == 1){
                    var alert = "<div class='alert alert-success alert-dismissable'><button type='button' class='close' " +
                      "data-dismiss='alert' aria-hidden='true'>&times;</button><strong>Success!</strong> Rejoiceable Added</div>";
                    var newRejoice = "<tr><td><strong><i class='glyphicon glyphicon-certificate'></i> Rejoiceable | " + json.type + 
                      "</strong><small class='pull-right'>" + moment(json.date, "YYYY-MM-DD H:mm:ss").fromNow() + 
                      "</small><br><span>" + json.info + "</span></td></tr>\n";
                    $("#activityTable tbody #loading").remove();
                    $("#activityTable tbody").prepend(newRejoice);
                  }
                  $("#flash").html(alert);
                  window.setTimeout(function() { 
                    $(".alert").fadeTo(1000, 0).slideUp(1000, function(){
                    $(this).remove(); 
                  })}, 4000);
                }
              );
              $('#rejoiceModal').modal('hide');*/
            }
          });
        });
      });
    </script>
    <div class="container">
    <div class="row">
      <h1 class="text-center">P2C Facebook Form</h1>
      <div id="formView" class="col-sm-12">
        <form class="form-horizontal" id="fbForm" role="form" action="" method="post">
        <div class="form-group">
          <label for="inputQ1" class="col-md-3 control-label">Question 1</label>
          <div class="col-md-9">
            <input type="text" class="form-control" id="inputQ1" name="inputQ1" placeholder="">
          </div>
        </div>
        <div class="form-group">
          <label for="inputQ2" class="col-md-3 control-label">Question 2</label>
          <div class="col-md-9">
            <textarea class="form-control" id="inputQ2" name="inputQ2" rows="3" placeholder=""></textarea>
          </div>
        </div>
        <div class="text-center">
          <input type="hidden" id="inputSubmitted" name="inputSubmitted" value="true">
          <button type="submit" class="btn btn-success">Submit</button>
        </div>
        </form>
      </div>
      <div id="resultView" class="col-sm-12 hiddenElement text-center">
        <h3>Thanks for filling out the survey</h3>
        <h5>Someone will contact you shortly</h5>
        <a href="#" id="fbshare" class="btn btn-primary">Share</a>
      </div>
    </div>
    <?php if ($user) { ?>
      Your user profile is
      <pre>
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre>
    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>
    <div id="fb-root"></div>
    </div>
  </body>
</html>
