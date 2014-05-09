<?php
  if($_POST["selectCampus"]){
    setcookie("campus", $_POST["selectCampus"], time()+60*60*24*30, COOKIE_PATH, COOKIE_DOMAIN);
  }
  elseif($_POST["selectSubmitted"]){
    setcookie("campus", "", time()-3600, COOKIE_PATH, COOKIE_DOMAIN);
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $title; ?></title>
    <link rel="shortcut icon" href="/images/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset='utf-8'>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/css/bootstrap.min.css" media="screen">
    <link rel="stylesheet" href="/css/datatables.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/css/daterangepicker-bs3.css">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/jquery.validate.min.js"></script>
    <script src="/js/moment.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/jquery.dataTables.js"></script> 
    <script src="/js/datatables.js"></script>
    <script src="/js/bootstrap-select.min.js"></script>
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="/js/daterangepicker.js"></script>
  </head>

  <body>
    <div id="wrap">
    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="https://pulse.p2c.com" class="navbar-brand active" style="margin:1px 0px; padding:0px;">
        <img class="img-responsive brand-logo fade" src="/images/pulse_logo_black.png"></img></a>
      </div>
    
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li><a href="https://pulse.p2c.com">Dashboard</a></li>
          <?php if(userAccess(STUDENT_VIS)){ ?><li><a href="https://pulse.p2c.com/people/<?php echo $pulse_id; ?>">Profile</a></li><?php } ?>
          <?php if(userAccess(LEADER_VIS)){ ?><li><a href="https://pulse.p2c.com/groups">Groups</a></li><?php } ?>
          <?php if(userAccess(STUDENT_VIS)){ ?><li <?php echo $activeDiscover; ?>><a href="/discover/">Discover</a></li><?php } ?>
          <?php if(userAccess(LEADER_VIS)){ ?><li><a href="https://pulse.p2c.com/campus_discipleship">Discipleship</a></li><?php } ?>
          <?php if(userAccess(STUDENT_VIS)){ ?><li <?php echo $activeInsights; ?>><a href="/insights/">Insights</a></li><?php } ?>
          <?php if(userAccess(LEADER_VIS)){ ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Connect <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="https://connect.p2c.com/sessions">My Connections</a></li>
              <li><a href="https://hub.p2c.com/node/10">Data Input</a></li>
            </ul>
          </li><?php } ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="?logout="> Logout of <?php echo $user["firstName"] . " " . $user["lastName"]; ?></a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </nav>

    <div id="header-title" class="page-header">
      <h1 class="text-center"><?php echo $title; ?></h1>
    </div>

      <?php //print_r($sends); ?>
      <?php
        (isset($access) ? checkUser($access) : checkUser());
      ?>
