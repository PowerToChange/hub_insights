<?php

  define('CONNECT_HOST', 'localhost');
  define('CONNECT_DB', 'StagingHubTest');
  define('CONNECT_USER', 'root');
  define('CONNECT_PASSWD', 'root');

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }
  else {
    $mysqli->query("CREATE TABLE IF NOT EXISTS cas_sessions (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, sessionID CHAR(26), data TEXT DEFAULT '', dateTouched INTEGER);");
  }
	
  // open SQLite sessions database and create sessions table if it doesn't exist
	//if ($db = new PDO('sqlite:application.db')) {
	//		$db->exec("CREATE TABLE IF NOT EXISTS sessions (id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, sessionID CHAR(26), data TEXT DEFAULT '', dateTouched INTEGER);");
	// } else {
	//		die('Unable to open database application.db');
	// }

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
      if(!$row = mysqli_fetch_assoc($result)) {
        $mysqli->query("INSERT INTO cas_sessions (sessionID, dateTouched) VALUES ('$sess_id', $CurrentTime);");
        return '';
      } else {
        $mysqli->query("UPDATE cas_sessions SET dateTouched = $CurrentTime WHERE sessionID = '$sess_id';");
        return $row['data'];
      }
    }

		/*$db = new PDO('sqlite:application.db');
		$result = $db->query("SELECT data FROM sessions WHERE sessionID = '$sess_id';");
		$CurrentTime = time();
		if (!$row = $result->fetch(PDO::FETCH_ASSOC)) {
			 $db->exec("INSERT INTO sessions (sessionID, dateTouched) VALUES ('$sess_id', $CurrentTime);");
			 return '';
		} else {
			 $db->exec("UPDATE sessions SET dateTouched = $CurrentTime WHERE sessionID = '$sess_id';");
			 return $row['data'];
		}*/
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
    return false;


		/*$db = new PDO('sqlite:application.db');
		$CurrentTime = time();
		$db->exec("UPDATE sessions SET data = '$data', dateTouched = $CurrentTime WHERE sessionID = '$sess_id';");
		return true;*/
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
    return false;


			/*$db = new PDO('sqlite:application.db');
			$db->exec("DELETE FROM sessions WHERE sessionID = '$sess_id';");
			return true;*/
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
    return false;

			/*$db = new PDO('sqlite:application.db');
			$CurrentTime = time();
			$db->exec("DELETE FROM sessions WHERE dateTouched + $sess_maxlifetime < $CurrentTime;");
			return true;*/
  }

	 session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
	 session_start();
?>