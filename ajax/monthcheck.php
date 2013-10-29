<?php
  include '../config/dbconstants.php';
  include '../config/columnnames.php';

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }

  date_default_timezone_set('America/Toronto');
  $month = date('m');
  $year = date('Y');
  $campus_id = $_GET["cid"];


  $result = array("type" => "false");
  $repQuery = "select civicrm_activity.id as 'ID', YEAR(civicrm_activity.activity_date_time) as 'YEAR',
    MONTH(civicrm_activity.activity_date_time) as 'MONTH', 
    " . MONTH . M_UNREC . " as 'UNRECORDED', " . MONTH . M_GROW . " as 'GROWING', 
    " . MONTH . M_MIN . " as 'MINISTERING', " . MONTH . M_MULT . " as 'MULTIPLYING' from civicrm_activity
    inner join " . MONTH . " on civicrm_activity.id = " . MONTH . ".entity_id
    inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
    inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
    where b.id = ? and activity_type_id = " . M_ID . "
    order by civicrm_activity.activity_date_time DESC;";
  if ($repStmt = $mysqli->prepare($repQuery)){
    $repStmt->bind_param("i", $campus_id);
    $repStmt->execute();
    $repStmt->bind_result($id_bind, $year_bind, $month_bind, $unrec_bind, $grow_bind, $min_bind, $mult_bind);
    if($repStmt->fetch()) {
      if($month == $month_bind && $year == $year_bind){
        $result = array("type" => "double", "aid" => $id_bind, "unrec" => $unrec_bind, "grow" => $grow_bind, "min" => $min_bind, "mult" => $mult_bind);
      }
      else {
        $result = array("type" => "autopop", "aid" => $id_bind, "unrec" => $unrec_bind, "grow" => $grow_bind, "min" => $min_bind, "mult" => $mult_bind);
      }
    }
  }

  echo json_encode($result);
?>