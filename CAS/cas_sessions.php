<?php

  define('CONNECT_HOST', 'localhost');
  define('CONNECT_DB', 'StagingHubTest');
  define('CONNECT_USER', 'root');
  define('CONNECT_PASSWD', 'root');

   /*****************************************
    * functions for handling single signout *
    *****************************************/
  function _cas_single_signout_callback($logoutTicket) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }

    if ($result = $mysqli->query("SELECT sessionID from cas_login_data WHERE logoutTicket = '$logoutTicket';")) {
      if($row = mysqli_fetch_assoc($result)) {
        unset($result);
        $mysqli->query("DELETE FROM cas_login_data WHERE sessionID = '" . $row['sessionID'] . "';");
        $mysqli->query("DELETE FROM cas_sessions WHERE sessionID = '" . $row['sessionID'] . "';");
      }
    }

      /*$db = new PDO('sqlite:application.db');
      $result = $db->query("SELECT sessionID from cas_login_data WHERE logoutTicket = '$logoutTicket';");
      if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
         unset($result);
         $db->exec("DELETE FROM cas_login_data WHERE sessionID = '" . $row['sessionID'] . "';");
         $db->exec("DELETE FROM sessions WHERE sessionID = '" . $row['sessionID'] . "';");
      }*/

  }

  function _cas_track_logout_ticket($logoutTicket) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }

    if ($result = $mysqli->query("CREATE TABLE IF NOT EXISTS cas_login_data (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, sessionID CHAR(26), logoutTicket CHAR(33));")) {
      $mysqli->query("INSERT INTO cas_login_data (sessionID, logoutTicket) VALUES ('".session_id()."', '$logoutTicket');");
    }

      // open SQLite sessions database and create cas_login_data table if it doesn't exist
      /*if ($db = new PDO('sqlite:application.db')) {
         $db->exec("CREATE TABLE IF NOT EXISTS cas_login_data (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, sessionID CHAR(26), logoutTicket CHAR(33));");
      } else {
         die('Unable to open database application.db');
      }

      $db->exec("INSERT INTO cas_login_data (sessionID, logoutTicket) VALUES ('".session_id()."', '$logoutTicket');");*/
  }
?>