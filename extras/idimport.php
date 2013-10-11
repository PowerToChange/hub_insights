<?php
  include '../config/dbconstants.php';
  include '../blackbox.php';

  $civicrm_id = 1;

  $method = array(1 => "1", 2 => "4", 3 => "3", 4 => "2", 5 => "5", 7 => "10",
    8 => "7", 9 => "6", 10 => "8", 11 => "9", 12 => "11", 13 => "11");
  $integrated = array(0 => "0", 1 => "10");
  $decisionQuery = "select * from cim_stats_prc";

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }

  echo "Import Started\n";
  $i = 0;

  if ($decisions = $mysqli->query($decisionQuery)) {
    while ($row = mysqli_fetch_assoc($decisions)) {
      $schoolID = 30412;
      $idQuery = "select id from civicrm_contact where external_identifier = " . $row["campus_id"];
      if ($result = $mysqli->query($idQuery)) {
        while ($school = mysqli_fetch_assoc($result)) {
          $schoolID = $school["id"];
        }
      }
      
      $params = array(
        "inputFirst" => $row["prc_firstName"], "inputCampus" => $schoolID, "inputDate" => $row["prc_date"], 
        "inputIntegrated" => $integrated[$row["prc_7upCompleted"]], "inputMethod" => $method[$row["prcMethod_id"]], 
        "inputWitness" => $row["prc_witnessName"], "inputStory" => $row["prc_notes"]
      );

      $succeeded = add_decision($params);
      if($succeeded == 1){
        echo "ID " . $row["prc_id"] . " successful!\n";
      }
      else {
        echo "ID " . $row["prc_id"] . " failed! Error: " . $succeeded . "\n";
      }

      $i++;
      /*if($i > 1){
        echo "Import Completed\n";
        exit;
      }*/
    }
    echo "Import Completed\n";
  }
?>