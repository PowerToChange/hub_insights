<?php
  include 'config/dbconstants.php';

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

  function getDecisions($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $campus = ($params["selectCampus"] ? " b.id = ? and" : "");
    $dates = getDates($params);

    $decisions = array();
    $idQuery = "select civicrm_activity.id as 'ID', DATE(civicrm_activity.activity_date_time) as 'DATE', a.`display_name` as 'BELIEVER', 
      a.first_name as 'B_FIRST', a.last_name as 'B_LAST', a.id as 'BELIEVER_ID',
      civicrm_value_rejoiceable_16.witnesses_171 as 'WITNESS', civicrm_value_rejoiceable_16.method_163 as 'METHOD',
      civicrm_activity.details as 'STORY', civicrm_activity.engagement_level as 'INTEGRATED',
      b.display_name as 'CAMPUS', b.id as 'CAMPUS_ID' from civicrm_activity
      inner join civicrm_value_rejoiceable_16 on civicrm_activity.id = civicrm_value_rejoiceable_16.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where" . $campus . " civicrm_activity.activity_date_time between ? and ? and
      activity_type_id = 47 and civicrm_value_rejoiceable_16.rejoiceable_143 = 4;";
    if ($idStmt = $mysqli->prepare($idQuery)){
      if($campus){
        $idStmt->bind_param("iss", $params["selectCampus"], $dates["start"], $dates["end"]);
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
    $campus = ($params["selectCampus"] ? " b.id = ? and" : "");
    $dates = getDates($params);

    $byMethod = array();
    $idQuery = "select civicrm_value_rejoiceable_16.method_163 as 'METHOD', count(*) as 'TOTAL',
      count(CASE civicrm_activity.engagement_level WHEN 10 then 1 ELSE NULL END) as 'P2C',
      count(CASE civicrm_activity.engagement_level WHEN 8 then 1 ELSE NULL END) as 'OTHER',
      count(CASE civicrm_activity.engagement_level WHEN 0 then 1 ELSE NULL END) as 'NOT' from civicrm_activity
      inner join civicrm_value_rejoiceable_16 on civicrm_activity.id = civicrm_value_rejoiceable_16.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where" . $campus . " civicrm_activity.activity_date_time between ? and ? and 
      activity_type_id = 47 and civicrm_value_rejoiceable_16.rejoiceable_143 = 4
      and civicrm_value_rejoiceable_16.method_163 is not null 
      group by civicrm_value_rejoiceable_16.method_163;";
    if ($idStmt = $mysqli->prepare($idQuery)){
      if($campus){
        $idStmt->bind_param("iss", $params["selectCampus"], $dates["start"], $dates["end"]);
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
      inner join civicrm_value_rejoiceable_16 on civicrm_activity.id = civicrm_value_rejoiceable_16.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact b on civicrm_activity_assignment.assignee_contact_id = b.id
      where civicrm_activity.activity_date_time between ? and ? and 
      activity_type_id = 47 and civicrm_value_rejoiceable_16.rejoiceable_143 = 4
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

  //*****************************************************************************************************

  function getEvents(){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $events = array();
    $eventQuery = "select civicrm_activity.id as 'ID', civicrm_activity.activity_date_time as 'DATE', 
      civicrm_activity.subject as 'NAME', civicrm_value_outreach_event_24.type_of_event_164 as 'TYPE',
      civicrm_value_outreach_event_24.total_attendance_165 as 'TOTAL', civicrm_activity.details as 'STORY',
      civicrm_value_outreach_event_24.non_christian_attendance_166 as 'NONCHRISTIAN',
      a.display_name as 'CAMPUS' from civicrm_activity
      inner join civicrm_value_outreach_event_24 on civicrm_activity.id = civicrm_value_outreach_event_24.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      where activity_date_time > '2013-08-01' and activity_type_id = 53;";
    if ($result = $mysqli->query($eventQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
      }
    }
    return $events;
  }

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