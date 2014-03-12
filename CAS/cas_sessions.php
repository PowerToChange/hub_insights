<?php

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
  }

  function _cas_track_logout_ticket($logoutTicket) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }

    if ($result = $mysqli->query("CREATE TABLE IF NOT EXISTS cas_login_data (id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, sessionID CHAR(64), logoutTicket CHAR(64));")) {
      $mysqli->query("INSERT INTO cas_login_data (sessionID, logoutTicket) VALUES ('".session_id()."', '$logoutTicket');");
    }
  }
?>