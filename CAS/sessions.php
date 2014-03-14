<?php

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }
  else {
    $mysqli->query("CREATE TABLE IF NOT EXISTS cas_sessions (id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT, sessionID CHAR(64), data TEXT DEFAULT '', dateTouched INTEGER);");
  }

	function sess_open($sess_path, $sess_name) {
    return true;
	}

	function sess_close() {
    return true;
	}

	function sess_read($sess_id) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }

    $CurrentTime = time();
    if ($result = $mysqli->query("SELECT data FROM cas_sessions WHERE sessionID = '$sess_id';")) {
      if(!($row = mysqli_fetch_assoc($result))) {
        $mysqli->query("INSERT INTO cas_sessions (sessionID, dateTouched) VALUES ('$sess_id', $CurrentTime);");
        return '';
      } else {
        $mysqli->query("UPDATE cas_sessions SET dateTouched = $CurrentTime WHERE sessionID = '$sess_id';");
        return $row['data'];
      }
    }
  }

	function sess_write($sess_id, $data) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $CurrentTime = time();
    if ($result = $mysqli->query("UPDATE cas_sessions SET data = '$data', dateTouched = $CurrentTime WHERE sessionID = '$sess_id';")) {
      return true;
    }
    return true;
  }

  function sess_destroy($sess_id) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $CurrentTime = time();
    if ($result = $mysqli->query("DELETE FROM cas_sessions WHERE sessionID = '$sess_id';")) {
      return true;
    }
    return true;
  }

  function sess_gc($sess_maxlifetime) {
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $CurrentTime = time();
    if ($result = $mysqli->query("DELETE FROM cas_sessions WHERE dateTouched + $sess_maxlifetime < $CurrentTime;")) {
      return true;
    }
    return true;
  }

	 session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
	 session_start();
?>