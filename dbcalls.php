<?php
  include 'config/dbconstants.php';
  include 'config/columnnames.php';

  function getDates($params){
    $dates = array();
    if($params["hiddenStart"] && $params["hiddenEnd"]){
      $dates["start"] = $params["hiddenStart"];
      $dates["end"] = $params["hiddenEnd"];
    }
    else {
      date_default_timezone_set('America/Toronto');   
      if(date("Y-m-d") < date("Y-09-01")){
        $dates["start"] = date('Y-09-01', strtotime('-1 years'));
        $dates["end"] = date('Y-08-31');
      }
      else {
        $dates["start"] = date('Y-09-01');
        $dates["end"] = date('Y-08-31', strtotime('+1 years'));
      }    
    }
    return $dates;
  }

  function getCampus($params){
    $campus = array();
    if($params["selectSubmitted"]){
      $campus["query"] = ($params["selectCampus"] ? " b.id = ? and" : "");
      $campus["id"] = ($params["selectCampus"] ?: "");
    }
    elseif($_COOKIE["campus"]){
      $campus["query"] = " b.id = ? and";
      $campus["id"] = $_COOKIE["campus"];
    }
    else{
      $campus["query"] = "";
      $campus["id"] = "";
    }
    return $campus;
  }

//****************************************************************************************************************

  function getDecisions($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $campus = getCampus($params);
    $dates = getDates($params);

    $decisions = array();
    $idQuery = "select civicrm_activity.id as 'ID', DATE(civicrm_activity.activity_date_time) as 'DATE', a.`display_name` as 'BELIEVER', 
      a.first_name as 'B_FIRST', a.last_name as 'B_LAST', a.id as 'BELIEVER_ID',
      " . REJOICEABLE . R_WITNESS . " as 'WITNESS', " . REJOICEABLE . R_METHOD . " as 'METHOD',
      civicrm_activity.details as 'STORY', civicrm_activity.engagement_level as 'INTEGRATED',
      b.display_name as 'CAMPUS', b.id as 'CAMPUS_ID' from civicrm_activity
      inner join " . REJOICEABLE . " on civicrm_activity.id = " . REJOICEABLE . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ? and
      activity_type_id = 47 and " . REJOICEABLE . R_TYPE . " = 4;";
    if ($idStmt = $mysqli->prepare($idQuery)){
      if($campus["query"]){
        $idStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $idStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $idStmt->execute();
      $idStmt->bind_result($id_bind, $date_bind, $believer_bind, $first_bind, $last_bind, $bid_bind, $witness_bind, 
        $method_bind, $story_bind, $integrated_bind, $campus_bind, $cid_bind);
      $i = 0;
      while ($idStmt->fetch()) {
        $decisions[$i] = array("ID" => $id_bind, "DATE" => $date_bind, "BELIEVER" => $believer_bind, "B_FIRST" => $first_bind, 
          "B_LAST" => $last_bind, "BELIEVER_ID" => $bid_bind, "WITNESS" => $witness_bind, "METHOD" => $method_bind, 
          "STORY" => $story_bind, "INTEGRATED" => $integrated_bind, "CAMPUS" => $campus_bind, "CAMPUS_ID" => $cid_bind);
        $i++;
      }
    }
    return $decisions;
  }

  function getDecByMethod($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $campus = getCampus($params);
    $dates = getDates($params);

    $byMethod = array();
    $idQuery = "select " . REJOICEABLE . R_METHOD . " as 'METHOD', count(*) as 'TOTAL',
      count(CASE civicrm_activity.engagement_level WHEN 10 then 1 ELSE NULL END) as 'P2C',
      count(CASE civicrm_activity.engagement_level WHEN 8 then 1 ELSE NULL END) as 'OTHER',
      count(CASE civicrm_activity.engagement_level WHEN 0 then 1 ELSE NULL END) as 'NOT' from civicrm_activity
      inner join " . REJOICEABLE . " on civicrm_activity.id = " . REJOICEABLE . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ? and
      activity_type_id = 47 and " . REJOICEABLE . R_TYPE . " = 4
      and " . REJOICEABLE . R_METHOD . " is not null
      group by " . REJOICEABLE . R_METHOD . ";";
    if ($idStmt = $mysqli->prepare($idQuery)){
      if($campus["query"]){
        $idStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $idStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $idStmt->execute();
      $idStmt->bind_result($method_bind, $total_bind, $p2c_bind, $other_bind, $not_bind);
      while ($idStmt->fetch()) {
        $byMethod[$method_bind] = array("TOTAL" => $total_bind, "P2C" => $p2c_bind, "OTHER" => $other_bind, "NOT" => $not_bind);
      }
    }
    return $byMethod;
  }

  function getDecBigPicture($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $dates = getDates($params);

    $bigPicture = array();
    $idQuery = "select b.id as 'CAMPUS_ID', count(*) as 'TOTAL',
      count(CASE civicrm_activity.engagement_level WHEN 10 then 1 ELSE NULL END) as 'P2C',
      count(CASE civicrm_activity.engagement_level WHEN 8 then 1 ELSE NULL END) as 'OTHER',
      count(CASE civicrm_activity.engagement_level WHEN 0 then 1 ELSE NULL END) as 'NOT' from civicrm_activity
      inner join " . REJOICEABLE . " on civicrm_activity.id = " . REJOICEABLE . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where civicrm_activity.activity_date_time between ? and ? and 
      activity_type_id = 47 and " . REJOICEABLE . R_TYPE . " = 4
      group by b.id;";
    if ($idStmt = $mysqli->prepare($idQuery)){
      $idStmt->bind_param("ss", $dates["start"], $dates["end"]);
      $idStmt->execute();
      $idStmt->bind_result($id_bind, $total_bind, $p2c_bind, $other_bind, $not_bind);
      while ($idStmt->fetch()) {
        $bigPicture[$id_bind] = array("TOTAL" => $total_bind, "P2C" => $p2c_bind, "OTHER" => $other_bind, "NOT" => $not_bind);
      }
    }
    return $bigPicture;
  }

//****************************************************************************************************************

  function getEvents($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $campus = getCampus($params);
    $dates = getDates($params);

    $events = array();
    $evQuery = "select civicrm_activity.id as 'ID', DATE(civicrm_activity.activity_date_time) as 'DATE',
      civicrm_activity.subject as 'NAME', " . EVENT . E_TYPE. " as 'TYPE', " . EVENT . E_TOTAL . " as 'TOTAL',
      civicrm_activity.details as 'STORY', " . EVENT . E_NON . " as 'NONCHRISTIAN',
      b.display_name as 'CAMPUS', b.id as 'CAMPUS_ID' from civicrm_activity
      inner join " . EVENT . " on civicrm_activity.id = " . EVENT . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ? and activity_type_id = 53;";
    if ($evStmt = $mysqli->prepare($evQuery)){
      if($campus["query"]){
        $evStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $evStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $evStmt->execute();
      $evStmt->bind_result($id_bind, $date_bind, $name_bind, $type_bind, $total_bind,
        $story_bind, $nonchristian_bind, $campus_bind, $cid_bind);
      $i = 0;
      while ($evStmt->fetch()) {
        $events[$i] = array("ID" => $id_bind, "DATE" => $date_bind, "NAME" => $name_bind,
          "TYPE" => $type_bind, "TOTAL" => $total_bind, "NONCHRISTIAN" => $nonchristian_bind,
          "STORY" => $story_bind, "CAMPUS" => $campus_bind, "CAMPUS_ID" => $cid_bind);
        $i++;
      }
    }
    return $events;
  }

  function getEventsByType($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $campus = getCampus($params);
    $dates = getDates($params);

    $byType = array();
    $evQuery = "select " . EVENT . E_TYPE . " as 'TYPE', sum(" . EVENT . E_TOTAL . ") as 'TOTAL',
      sum(" . EVENT . E_NON . ") as 'NONCHRISTIAN' from civicrm_activity
      inner join " . EVENT . " on civicrm_activity.id = " . EVENT . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ?
      and " . EVENT . E_TYPE . " is not null and activity_type_id = 53
      group by " . EVENT . E_TYPE . ";";
    if ($evStmt = $mysqli->prepare($evQuery)){
      if($campus["query"]){
        $evStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $evStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $evStmt->execute();
      $evStmt->bind_result($type_bind, $total_bind, $nonchristian_bind);
      while ($evStmt->fetch()) {
        $byType[$type_bind] = array("TOTAL" => $total_bind, "NONCHRISTIAN" => $nonchristian_bind);
      }
    }
    return $byType;
  }

//****************************************************************************************************************

  function getMonthly(){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $reports = array();
    $repQuery = "select civicrm_activity.id as 'ID', civicrm_activity.activity_date_time as 'DATE', 
      civicrm_value_monthly_report_school_25.unrecorded_engagements_167 as 'UNRECORDED',
      civicrm_value_monthly_report_school_25.growing_disciples_168 as 'GROWING',
      civicrm_value_monthly_report_school_25.ministering_disciples_169 as 'MINISTERING',
      civicrm_value_monthly_report_school_25.multiplying_disciples_170 as 'MULTIPLYING',
      a.display_name as 'CAMPUS' from civicrm_activity
      inner join civicrm_value_monthly_report_school_25 on civicrm_activity.id = civicrm_value_monthly_report_school_25.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      where activity_date_time > '2013-08-01' and activity_type_id = 54;";
    if ($result = $mysqli->query($repQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
      }
    }
    return $reports;
  }

  function getSchools(){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $schools = array();
    $schoolQuery = "select school.`organization_name` as 'SCHOOL', school.`id` as 'ID', school.external_identifier as 'PULSEID' from civicrm_contact school
      inner join civicrm_value_school_info_10 on civicrm_value_school_info_10.entity_id = school.id
      where school.contact_sub_type = 'School' and civicrm_value_school_info_10.do_we_have_a_ministry_presence_h_73 = 'Yes' order by school.organization_name asc;";
    if ($result = $mysqli->query($schoolQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        $schools[$row["ID"]] = $row["SCHOOL"];
      }
    }
    return $schools;
  }

?>