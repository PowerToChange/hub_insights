<?php
  include '../config/dbconstants.php';
  include '../config/columnnames.php';

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }

  date_default_timezone_set('America/Toronto');
  $dates = array();
  if(date("Y-m-d") < date("Y-09-01")){
    $dates["start"] = date('Y-09-01', strtotime('-1 years'));
    $dates["end"] = date('Y-08-31');
  }
  else {
    $dates["start"] = date('Y-09-01');
    $dates["end"] = date('Y-08-31', strtotime('+1 years'));
  }

  $results = array("coords" => array(), "names" => array(), "counts" => array());
  $idQuery = "select b.display_name as 'NAME', count(*) as 'COUNT',
    address.geo_code_1 as 'LAT', address.geo_code_2 as 'LONG' from civicrm_activity
    inner join " . REJOICEABLE . " on civicrm_activity.id = " . REJOICEABLE . ".entity_id
    inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
    inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
    inner join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
    inner join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
    inner join civicrm_address address on b.id = address.contact_id
    where civicrm_activity.activity_date_time between ? and ? and 
    activity_type_id = " . R_ID . " and " . REJOICEABLE . R_TYPE . " = 4
    group by b.id;";
  if ($idStmt = $mysqli->prepare($idQuery)){
    $idStmt->bind_param("ss", $dates["start"], $dates["end"]);
    $idStmt->execute();
    $idStmt->bind_result($name_bind, $count_bind, $lat_bind, $long_bind);
    while ($idStmt->fetch()) {
      $results["coords"][] = array($lat_bind, $long_bind);
      $results["names"][] = utf8_encode($name_bind);
      $results["counts"][] = $count_bind;
    }
  }

  echo json_encode($results);
?>