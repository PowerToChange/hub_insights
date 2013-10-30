<?php
  include '../blackbox.php';
  include '../dbcalls.php';

  $civicrm_id = 1;

  function findCampus($reports, $cid){
    foreach($reports as $key => $report){
      if($report["CAMPUS_ID"] === $cid){
        return $key;
      }
    }
    return false;
  }

  date_default_timezone_set('America/Toronto');
  $start = date('Y-m-d', strtotime('first day of previous month'));
  $end = date('Y-m-d', strtotime('last day of previous month'));

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }

  echo "Import Started\n";
  $params = array("hiddenStart" => $start, "hiddenEnd" => $end);
  $reports = getMonthly($params);
  //print_r($reports);

  $schools = getSchools();
  foreach($schools as $id => $label){
    if(findCampus($reports, $id)){
      echo "School report exists for " . $label . "\n";
    }
    else {
      echo "Missing report for " . $label . "\n";
      $stats = array("inputCampus" => $id, "inputDate" => $end, "inputUnRec" => 0, "inputAuto" => 1,
            "inputGrow" => 0, "inputMin" => 0, "inputMult" => 0);
      $repQuery = "select civicrm_activity.id as 'ID', " . MONTH . M_GROW . " as 'GROWING',
        " . MONTH . M_MIN . " as 'MINISTERING', " . MONTH . M_MULT . " as 'MULTIPLYING' from civicrm_activity
        inner join " . MONTH . " on civicrm_activity.id = " . MONTH . ".entity_id
        inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
        inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
        where b.id = ? and activity_type_id = " . M_ID . "
        order by civicrm_activity.activity_date_time DESC;";
      if ($repStmt = $mysqli->prepare($repQuery)){
        $repStmt->bind_param("i", $id);
        $repStmt->execute();
        $repStmt->bind_result($id_bind, $grow_bind, $min_bind, $mult_bind);
        if($repStmt->fetch()) {
          $stats = array("inputCampus" => $id, "inputDate" => $end, "inputUnRec" => 0, "inputAuto" => 1,
            "inputGrow" => $grow_bind, "inputMin" => $min_bind, "inputMult" => $mult_bind);
        }
      }
      $monResult = add_monthly($stats);
      if($monResult == 1){
        echo "MONTH REPORT FOR  " . $label . " ADDED SUCCESSFULLY!\n";
      }
      else {
        echo "MONTH REPORT FOR " . $label . " FAILED! ERROR: " . $monResult . "\n";
      }
      exit;
    }
  }
  /*$decisionQuery = "select * from cim_stats_prc";
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
      if($i > 1){
        echo "Import Completed\n";
        exit;
      }
    }
    echo "Import Completed\n";
  }*/
?>