<?php
  include '../config/dbconstants.php';
  include '../blackbox.php';

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }

  $row = 0;
  if (($handle = fopen("pulsemonthly.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $schoolID = 30412;
      $idQuery = "select id from civicrm_contact where external_identifier = " . $data[0];
      if ($result = $mysqli->query($idQuery)) {
        while ($school = mysqli_fetch_assoc($result)) {
          $schoolID = $school["id"];
        }
      }

      $monthly = array("inputCampus" => $schoolID, "inputDate" => $data[1], "inputUnrec" => $data[2], 
        "inputGrow" => $data[3], "inputMin" => $data[4], "inputMult" => $data[5], "inputAuto" => "0");
      $monResult = $add_monthly($monthly);
      if($monResult == 1){
        echo "MONTHLY ROW " . $row . " successful!\n";
      }
      else {
        echo "MONTHLY ROW " . $row . " failed! Error: " . $monResult . "\n";
      }

      if($data[6] != 0){
        $event = array("inputCampus" => $schoolID, "inputName" => "Imported Pulse Event", "inputDate" => $data[1],
          "inputType" => "11", "inputTotal" => $data[6], "inputNon" => $data[6]);
        $evResult = $add_event($event);
        if($evResult == 1){
          echo "EVENT ROW " . $row . " successful!\n";
        }
        else {
          echo "EVENT ROW " . $row . " failed! Error: " . $evResult . "\n";
        }
      }

      $row++;
    }
    fclose($handle);
  }
?>