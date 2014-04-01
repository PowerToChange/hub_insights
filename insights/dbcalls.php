<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/config/dbconstants.php';
  include $_SERVER['DOCUMENT_ROOT'].'/config/columnnames.php';

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
    $mysqli->set_charset("utf8");
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
      activity_type_id = " . R_ID . " and " . REJOICEABLE . R_TYPE . " = 4;";
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
    $mysqli->set_charset("utf8");
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
      activity_type_id = " . R_ID . " and " . REJOICEABLE . R_TYPE . " = 4
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
    $mysqli->set_charset("utf8");
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
      activity_type_id = " . R_ID . " and " . REJOICEABLE . R_TYPE . " = 4
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
    $mysqli->set_charset("utf8");
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
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ?
      and activity_type_id = " . E_ID . ";";
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
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $byType = array();
    $evQuery = "select " . EVENT . E_TYPE . " as 'TYPE', sum(" . EVENT . E_TOTAL . ") as 'TOTAL',
      sum(" . EVENT . E_NON . ") as 'NONCHRISTIAN' from civicrm_activity
      inner join " . EVENT . " on civicrm_activity.id = " . EVENT . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ?
      and " . EVENT . E_TYPE . " is not null and activity_type_id = " . E_ID . "
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

  function getMonthly($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $reports = array();
    $repQuery = "select civicrm_activity.id as 'ID', DATE(civicrm_activity.activity_date_time) as 'DATE',
      " . MONTH . M_UNREC . " as 'UNRECORDED', " . MONTH . M_GROW . " as 'GROWING',
      " . MONTH . M_MIN . " as 'MINISTERING', " . MONTH . M_MULT . " as 'MULTIPLYING',
      " . MONTH . M_AUTO . " as 'AUTOGEN', b.display_name as 'CAMPUS', b.id as 'CAMPUS_ID' from civicrm_activity
      inner join " . MONTH . " on civicrm_activity.id = " . MONTH . ".entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      where" . $campus["query"] . " civicrm_activity.activity_date_time between ? and ?
      and activity_type_id = " . M_ID . ";";
    if ($repStmt = $mysqli->prepare($repQuery)){
      if($campus["query"]){
        $repStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $repStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $repStmt->execute();
      $repStmt->bind_result($id_bind, $date_bind, $unrec_bind, $grow_bind, $min_bind,
        $mult_bind, $auto_bind, $campus_bind, $cid_bind);
      $i = 0;
      while ($repStmt->fetch()) {
        $reports[$i] = array("ID" => $id_bind, "DATE" => $date_bind, "UNRECORDED" => $unrec_bind,
          "GROWING" => $grow_bind, "MINISTERING" => $min_bind, "MULTIPLYING" => $mult_bind,
          "AUTOGEN" => $auto_bind, "CAMPUS" => $campus_bind, "CAMPUS_ID" => $cid_bind);
        $i++;
      }
    }

    return $reports;
  }

  function getMSBigPicture($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $dates = getDates($params);

    $bigPicture = array();
    $surveyQuery = "select school.id as 'CAMPUS_ID', count(*) as 'SURVEY',
      count(CASE WHEN civicrm_activity.engagement_level >= 5 AND civicrm_activity.engagement_level <= 10 then 1 ELSE NULL END) as 'RESULT' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact school on civicrm_relationship.`contact_id_b` = school.id
      inner join civicrm_value_school_info_10 on civicrm_value_school_info_10.entity_id = school.id
      where activity_type_id = 32 and civicrm_activity.activity_date_time between ? and ?
      and civicrm_value_school_info_10.do_we_have_a_ministry_presence_h_73 = 'Yes'
      group by school.id;";
    if ($surveyStmt = $mysqli->prepare($surveyQuery)){
      $surveyStmt->bind_param("ss", $dates["start"], $dates["end"]);
      $surveyStmt->execute();
      $surveyStmt->bind_result($id_bind, $survey_bind, $result_bind);
      while ($surveyStmt->fetch()) {
        $bigPicture[$id_bind] = array("SURVEY" => $survey_bind, "RESULT" => $result_bind);
      }
    }

    $msQuery = "select b.id as 'ID', sum(" . EVENT . E_TOTAL . ") as 'TOTAL',
      sum(" . EVENT . E_NON . ") as 'NONCHRISTIAN', sum(" . MONTH . M_UNREC . ") as 'UNREC'
      from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      inner join civicrm_value_school_info_10 on civicrm_value_school_info_10.entity_id = b.id
      left join " . EVENT . " on civicrm_activity.id = " . EVENT . ".entity_id
      left join " . MONTH . " on civicrm_activity.id = " . MONTH . ".entity_id
      where b.contact_sub_type = 'School' and civicrm_activity.activity_date_time between ? and ?
      and civicrm_value_school_info_10.do_we_have_a_ministry_presence_h_73 = 'Yes'
      group by b.id";
    if ($msStmt = $mysqli->prepare($msQuery)){
      $msStmt->bind_param("ss", $dates["start"], $dates["end"]);
      $msStmt->execute();
      $msStmt->bind_result($id_bind, $total_bind, $event_bind, $unrec_bind);
      while ($msStmt->fetch()) {
        if(is_array($bigPicture[$id_bind])){
          $bigPicture[$id_bind] = array_merge($bigPicture[$id_bind], array("TOTAL" => $total_bind, "EVENT" => $event_bind, "UNREC" => $unrec_bind));
        }
        else {
          $bigPicture[$id_bind] = array("TOTAL" => $total_bind, "EVENT" => $event_bind, "UNREC" => $unrec_bind);
        }
      }
    }

    return $bigPicture;
  }

  function getMSByCampus($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $byCampus = array();
    $surveyQuery = "select YEAR(civicrm_activity.activity_date_time) as 'YEAR', MONTH(civicrm_activity.activity_date_time) as 'MONTH', count(*) as 'SURVEY',
      count(CASE WHEN civicrm_activity.engagement_level >= 5 AND civicrm_activity.engagement_level <= 10 then 1 ELSE NULL END) as 'RESULT' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.contact_id_b = b.id
      where" . $campus["query"] . " activity_type_id = 32 and civicrm_activity.activity_date_time between ? and ?
      group by YEAR(civicrm_activity.activity_date_time), MONTH(civicrm_activity.activity_date_time);";
    if ($surveyStmt = $mysqli->prepare($surveyQuery)){
      if($campus["query"]){
        $surveyStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $surveyStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $surveyStmt->execute();
      $surveyStmt->bind_result($year_bind, $month_bind, $survey_bind, $result_bind);
      while ($surveyStmt->fetch()) {
        $date = $year_bind . "-" . $month_bind;
        $byCampus[$date] = array("SURVEY" => $survey_bind, "RESULT" => $result_bind);
      }
    }

    $msQuery = "select YEAR(civicrm_activity.activity_date_time) as 'YEAR', MONTH(civicrm_activity.activity_date_time) as 'MONTH', 
      sum(" . EVENT . E_TOTAL . ") as 'TOTAL', sum(" . EVENT . E_NON . ") as 'NONCHRISTIAN',
      sum(" . MONTH . M_UNREC . ") as 'UNREC', sum(" . MONTH . M_GROW . ") as 'GROWING',
      sum(" . MONTH . M_MIN . ") as 'MINISTERING', sum(" . MONTH . M_MULT . ") as 'MULTIPLYING'
      from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact b on civicrm_activity_target.target_contact_id = b.id
      left join " . EVENT . " on civicrm_activity.id = " . EVENT . ".entity_id
      left join " . MONTH . " on civicrm_activity.id = " . MONTH . ".entity_id
      where" . $campus["query"] . " b.contact_sub_type = 'School' and civicrm_activity.activity_date_time between ? and ?
      group by YEAR(civicrm_activity.activity_date_time), MONTH(civicrm_activity.activity_date_time)";
    if ($msStmt = $mysqli->prepare($msQuery)){
      if($campus["query"]){
        $msStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $msStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $msStmt->execute();
      $msStmt->bind_result($year_bind, $month_bind, $total_bind, $event_bind, $unrec_bind, $grow_bind, $min_bind, $mult_bind);
      while ($msStmt->fetch()) {
        $date = $year_bind . "-" . $month_bind;
        if(is_array($byCampus[$date])){
          $byCampus[$date] = array_merge($byCampus[$date], array("TOTAL" => $total_bind, "EVENT" => $event_bind,
            "UNREC" => $unrec_bind, "GROW" => $grow_bind, "MIN" => $min_bind, "MULT" => $mult_bind));
        }
        else {
          $byCampus[$date] = array("TOTAL" => $total_bind, "EVENT" => $event_bind,
            "UNREC" => $unrec_bind, "GROW" => $grow_bind, "MIN" => $min_bind, "MULT" => $mult_bind);
        }
      }
    }

    return $byCampus;
  }

//************************************************DISCOVER***********************************************************

  function getDCByMonth($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $byMonth = array();
    $whereClause = " where ";
    if($campus["id"]){
      $whereClause = " inner join civicrm_relationship on civicrm_activity_target.target_contact_id = civicrm_relationship.contact_id_a
        where civicrm_relationship.relationship_type_id = 10 AND civicrm_relationship.contact_id_b = ? AND civicrm_relationship.is_active = 1 AND ";
    }
    $activityQuery = "select YEAR(civicrm_activity.activity_date_time) as 'YEAR', MONTH(civicrm_activity.activity_date_time) as 'MONTH',
      count(CASE WHEN civicrm_activity.activity_type_id >= 2 AND civicrm_activity.activity_type_id <= 4 then 1 ELSE NULL END) as 'INTERACTIONS',
      count(CASE WHEN civicrm_activity.activity_type_id = 47 then 1 ELSE NULL END) as 'REJOICEABLES',
      count(CASE WHEN civicrm_activity.activity_type_id = 32 then 1 ELSE NULL END) as 'SURVEYS' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id " . $whereClause . "
      civicrm_activity_target.target_contact_id IN (select contact_id_b from civicrm_relationship where  civicrm_relationship.relationship_type_id = 16)
      AND civicrm_activity.activity_date_time between ? and ?
      GROUP BY YEAR(civicrm_activity.activity_date_time), MONTH(civicrm_activity.activity_date_time);";
    if ($activityStmt = $mysqli->prepare($activityQuery)){
      if($campus["id"]){
        $activityStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $activityStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $activityStmt->execute();
      $activityStmt->bind_result($year_bind, $month_bind, $int_bind, $rejoice_bind, $survey_bind);
      while ($activityStmt->fetch()) {
        $date = $year_bind . "-" . $month_bind;
        $byMonth[$date] = array("INTERACTIONS" => $int_bind, "REJOICEABLES" => $rejoice_bind, "SURVEYS" => $survey_bind);
      }
    }

    $newWhere = " where ";
    if($campus["id"]){
      $newWhere = " inner join civicrm_relationship c on r.contact_id_b = c.contact_id_a
        where c.relationship_type_id = 10 AND c.contact_id_b = ? AND c.is_active = 1 AND ";
    }
    $newQuery = "select YEAR(r.start_date) as 'YEAR', MONTH(r.start_date) as 'MONTH', count(r.contact_id_b) as 'NEW' from civicrm_relationship r
      " . $newWhere . " r.relationship_type_id = 16 and r.start_date between ? and ? AND
      r.start_date = (select min(start_date) from civicrm_relationship where contact_id_b = r.contact_id_b)
      GROUP BY YEAR(r.start_date), MONTH(r.start_date);";
    if ($newStmt = $mysqli->prepare($newQuery)){
      if($campus["id"]){
        $newStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $newStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $newStmt->execute();
      $newStmt->bind_result($year_bind, $month_bind, $new_bind);
      while ($newStmt->fetch()) {
        $date = $year_bind . "-" . $month_bind;
        if(is_array($byMonth[$date])){
          $byMonth[$date] = array_merge($byMonth[$date], array("NEW" => $new_bind));
        }
        else {
          $byMonth[$date] = array("NEW" => $new_bind);
        }
      }
    }

    return $byMonth;
  }

  function getDCByPerson($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $byPerson = array();
    $whereClause = " where ";
    if($campus["id"]){
      $whereClause = " inner join civicrm_relationship c on civicrm_activity_target.target_contact_id = c.contact_id_a
        where c.relationship_type_id = 10 AND c.contact_id_b = ? AND ";
    }
    $activityQuery = "select civicrm_contact.id as 'ID', civicrm_contact.display_name as 'NAME',
      count(CASE WHEN civicrm_activity.activity_type_id >= 2 AND civicrm_activity.activity_type_id <= 4 then 1 ELSE NULL END) as 'INTERACTIONS',
      count(CASE WHEN civicrm_activity.activity_type_id = 47 then 1 ELSE NULL END) as 'REJOICEABLES',
      count(CASE WHEN civicrm_activity.activity_type_id = 32 then 1 ELSE NULL END) as 'SURVEYS' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_relationship on civicrm_activity_target.target_contact_id = civicrm_relationship.contact_id_b
      inner join civicrm_contact on civicrm_activity.source_contact_id = civicrm_contact.id " . $whereClause . "
      civicrm_relationship.relationship_type_id = 16 and civicrm_contact.id = civicrm_relationship.contact_id_a
      AND civicrm_activity.activity_date_time between ? and ?
      GROUP BY civicrm_contact.id, civicrm_contact.display_name;";
    if ($activityStmt = $mysqli->prepare($activityQuery)){
      if($campus["id"]){
        $activityStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $activityStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $activityStmt->execute();
      $activityStmt->bind_result($id_bind, $name_bind, $int_bind, $rejoice_bind, $survey_bind);
      while ($activityStmt->fetch()) {
        $byPerson[$id_bind] = array("NAME" => $name_bind, "INTERACTIONS" => $int_bind, "REJOICEABLES" => $rejoice_bind, "SURVEYS" => $survey_bind);
      }
    }

    $newWhere = " where ";
    if($campus["id"]){
      $newWhere = " inner join civicrm_relationship c on r.contact_id_b = c.contact_id_a
        where c.relationship_type_id = 10 AND c.contact_id_b = ? AND ";
    }
    $newQuery = "select civicrm_contact.id as 'ID', civicrm_contact.display_name as 'NAME', count(r.contact_id_b) as 'NEW' from civicrm_relationship r
      inner join civicrm_contact on r.contact_id_a = civicrm_contact.id
      " . $newWhere . " r.relationship_type_id = 16 and r.start_date between ? and ? AND
      r.start_date = (select min(start_date) from civicrm_relationship where contact_id_b = r.contact_id_b)
      GROUP BY civicrm_contact.id, civicrm_contact.display_name;";
    if ($newStmt = $mysqli->prepare($newQuery)){
      if($campus["id"]){
        $newStmt->bind_param("iss", $campus["id"], $dates["start"], $dates["end"]);
      }
      else{
        $newStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $newStmt->execute();
      $newStmt->bind_result($id_bind, $name_bind, $new_bind);
      while ($newStmt->fetch()) {
        if(is_array($byPerson[$id_bind])){
          $byPerson[$id_bind] = array_merge($byPerson[$id_bind], array("NEW" => $new_bind));
        }
        else {
          $byPerson[$id_bind] = array("NAME" => $name_bind, "NEW" => $new_bind);
        }
      }
    }

    return $byPerson;
  }

  function getDCThresholds($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $thresholds = array();
    $whereClause = " where ";
    if($campus["id"]){
      $whereClause = " inner join civicrm_relationship school on civicrm_value_discover_info_11.entity_id = school.contact_id_a
        where school.relationship_type_id = 10 AND school.contact_id_b = ? AND ";
    }
    $thresholdQuery = "select next_step_124 as 'THRESHOLDS', count(*) as 'COUNT' from civicrm_value_discover_info_11
      inner join civicrm_relationship disc on civicrm_value_discover_info_11.entity_id = disc.contact_id_b and disc.relationship_type_id = 16
      " . $whereClause . " disc.start_date between ? and ? or disc.end_date between ? and ? or
      (disc.start_date < ? and disc.end_date is null) or (disc.start_date < ? and disc.end_date > ?)
      group by next_step_124;";
    if ($thresholdStmt = $mysqli->prepare($thresholdQuery)){
      if($campus["id"]){
        $thresholdStmt->bind_param("isssssss", $campus["id"], $dates["start"], $dates["end"], $dates["start"],
          $dates["end"], $dates["start"], $dates["start"], $dates["end"]);
      }
      else{
        $thresholdStmt->bind_param("sssssss", $dates["start"], $dates["end"], $dates["start"],
          $dates["end"], $dates["start"], $dates["start"], $dates["end"]);
      }
      $thresholdStmt->execute();
      $thresholdStmt->bind_result($threshold_bind, $count_bind);
      while ($thresholdStmt->fetch()) {
        $thresholds[$threshold_bind] = $count_bind;
      }
    }

    return $thresholds;
  }

  function getActiveDiscover(){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");

    $count = "0";
    $activeQuery = "select count(distinct contact_id_b) as 'COUNT' from civicrm_relationship
      where relationship_type_id = 16 and is_active = 1;";
    if ($activeStmt = $mysqli->prepare($activeQuery)){
      $activeStmt->execute();
      $activeStmt->bind_result($count_bind);
      while ($activeStmt->fetch()) {
        $count = $count_bind;
      }
    }

    return $count;
  }

//*************************************************SURVEY***********************************************************

  function getNationalPriority($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $priorityQuery = "select school.id as 'ID', school.`organization_name` as 'SCHOOL', 
      count(CASE civicrm_activity.priority_id WHEN 1 then 1 ELSE NULL END) as 'HOT', 
      count(CASE civicrm_activity.priority_id WHEN 2 then 1 ELSE NULL END) as 'MEDIUM',
      count(CASE civicrm_activity.priority_id WHEN 3 then 1 ELSE NULL END) as 'MILD',
      count(CASE civicrm_activity.priority_id WHEN 4 then 1 ELSE NULL END) as 'NOT INTERESTED',
      count(CASE civicrm_activity.priority_id WHEN 5 then 1 ELSE NULL END) as 'N/A',
      count(*) as 'TOTAL' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact school on civicrm_relationship.`contact_id_b` = school.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32 and civicrm_relationship.`relationship_type_id` = 10
      and civicrm_relationship.is_active = 1 " . $surveyAddon . "
      group by school.`organization_name`;";
    if ($priorityStmt = $mysqli->prepare($priorityQuery)){
      if($surveyAddon != ""){
        $priorityStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      else{
        $priorityStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $priorityStmt->execute();
      $priorityStmt->bind_result($id_bind, $school_bind, $hot_bind, $medium_bind, $mild_bind, $not_bind, $na_bind, $total_bind);
      while ($priorityStmt->fetch()) {
        $report[$id_bind] = array("SCHOOL" => $school_bind, "HOT" => $hot_bind, "MEDIUM" => $medium_bind, 
          "MILD" => $mild_bind, "NOT" => $not_bind, "NA" => $na_bind, "TOTAL" => $total_bind);
      }
    }

    return $report;
  }

  function getNationalFollowup($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $followupQuery = "select school.id as 'ID', school.`organization_name` as 'SCHOOL', 
      count(CASE civicrm_activity.status_id WHEN 4 then 1 ELSE NULL END) as 'UNCONTACTED', 
      count(CASE civicrm_activity.status_id WHEN 3 then 1 ELSE NULL END) as 'IN PROGRESS',
      count(CASE civicrm_activity.status_id WHEN 2 then 1 ELSE NULL END) as 'COMPLETED',
      count(*) as 'TOTAL' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact school on civicrm_relationship.`contact_id_b` = school.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32 and civicrm_relationship.`relationship_type_id` = 10
      and civicrm_relationship.is_active = 1 " . $surveyAddon . "
      group by school.`organization_name`;";
    if ($followupStmt = $mysqli->prepare($followupQuery)){
      if($surveyAddon != ""){
        $followupStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      else{
        $followupStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $followupStmt->execute();
      $followupStmt->bind_result($id_bind, $school_bind, $un_bind, $in_bind, $com_bind, $total_bind);
      while ($followupStmt->fetch()) {
        $report[$id_bind] = array("SCHOOL" => $school_bind, "UNCONTACTED" => $un_bind, 
          "IN PROGRESS" => $in_bind, "COMPLETED" => $com_bind, "TOTAL" => $total_bind);
      }
    }

    return $report;
  }

  function getPriorityBreakdown($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $breakdownQuery = "select civicrm_activity.`priority_id` as PRIORITY, 
      count(CASE civicrm_activity.status_id WHEN 4 then 1 ELSE NULL END) as 'UNCONTACTED', 
      count(CASE civicrm_activity.status_id WHEN 3 then 1 ELSE NULL END) as 'IN PROGRESS',
      count(CASE civicrm_activity.status_id WHEN 2 then 1 ELSE NULL END) as 'COMPLETED',
      count(*) as 'TOTAL' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.`contact_id_b` = b.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32 and civicrm_relationship.`relationship_type_id` = 10
      " . $campus["query"] . $survyAddon . " group by civicrm_activity.`priority_id`";
    if ($breakdownStmt = $mysqli->prepare($breakdownQuery)){
      if($surveyAddon && $campus["id"]){
        $breakdownStmt->bind_param("ssii", $dates["start"], $dates["end"], $campus["id"], $params["selectSurvey"]);
      }
      elseif($surveyAddon){
        $breakdownStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      elseif($campus["id"]){
        $breakdownStmt->bind_param("ssi", $dates["start"], $dates["end"], $campus["id"]);
      }
      else{
        $breakdownStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $breakdownStmt->execute();
      $breakdownStmt->bind_result($priority_bind, $un_bind, $in_bind, $com_bind, $total_bind);
      while ($breakdownStmt->fetch()) {
        $report[$priority_bind] = array("UNCONTACTED" => $un_bind, 
          "IN PROGRESS" => $in_bind, "COMPLETED" => $com_bind, "TOTAL" => $total_bind);
      }
    }

    return $report;
  }

  function getVolunteers($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $volunteerQuery = "select v.sort_name as 'NAME', 
      count(CASE civicrm_activity.status_id WHEN 4 then 1 ELSE NULL END) as 'UNCONTACTED', 
      count(CASE civicrm_activity.status_id WHEN 3 then 1 ELSE NULL END) as 'IN PROGRESS',
      count(CASE civicrm_activity.status_id WHEN 2 then 1 ELSE NULL END) as 'COMPLETED',
      count(*) as 'TOTAL' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact v on civicrm_activity_assignment.assignee_contact_id = v.id
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.`contact_id_b` = b.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32 and civicrm_relationship.`relationship_type_id` = 10
      " . $campus["query"] . $survyAddon . " group by v.sort_name";
    if ($volunteerStmt = $mysqli->prepare($volunteerQuery)){
      if($surveyAddon && $campus["id"]){
        $volunteerStmt->bind_param("ssii", $dates["start"], $dates["end"], $campus["id"], $params["selectSurvey"]);
      }
      elseif($surveyAddon){
        $volunteerStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      elseif($campus["id"]){
        $volunteerStmt->bind_param("ssi", $dates["start"], $dates["end"], $campus["id"]);
      }
      else{
        $volunteerStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $volunteerStmt->execute();
      $volunteerStmt->bind_result($name_bind, $un_bind, $in_bind, $com_bind, $total_bind);
      while ($volunteerStmt->fetch()) {
        $report[] = array("NAME" => $name_bind, "UNCONTACTED" => $un_bind, 
          "IN PROGRESS" => $in_bind, "COMPLETED" => $com_bind, "TOTAL" => $total_bind);
      }
    }

    return $report;
  }

  function getRejoiceables($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $rejoiceQuery = "select civicrm_value_rejoiceable_16.rejoiceable_143 as 'TYPE', count(*) as 'COUNT' from civicrm_activity
      inner join civicrm_value_rejoiceable_16 on civicrm_activity.id = civicrm_value_rejoiceable_16.entity_id
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.`contact_id_b` = b.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 47 and " . REJOICEABLE . R_SURVEY . " is not null
      and civicrm_value_rejoiceable_16.rejoiceable_143 is not null " . $campus["query"] . $survyAddon . " group by " . REJOICEABLE . R_TYPE;
    if ($rejoiceStmt = $mysqli->prepare($rejoiceQuery)){
      if($surveyAddon && $campus["id"]){
        $rejoiceStmt->bind_param("ssii", $dates["start"], $dates["end"], $campus["id"], $params["selectSurvey"]);
      }
      elseif($surveyAddon){
        $rejoiceStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      elseif($campus["id"]){
        $rejoiceStmt->bind_param("ssi", $dates["start"], $dates["end"], $campus["id"]);
      }
      else{
        $rejoiceStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $rejoiceStmt->execute();
      $rejoiceStmt->bind_result($type_bind, $count_bind);
      while ($rejoiceStmt->fetch()) {
        $report[$type_bind] = $count_bind;
      }
    }

    return $report;
  }

  function getSurveyResults($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $resultQuery = "select civicrm_activity.engagement_level as TYPE, count(*) as COUNT from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.`contact_id_b` = b.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32
      and civicrm_activity.engagement_level is not null " . $campus["query"] . $survyAddon . " group by civicrm_activity.engagement_level";
    if ($resultStmt = $mysqli->prepare($resultQuery)){
      if($surveyAddon && $campus["id"]){
        $resultStmt->bind_param("ssii", $dates["start"], $dates["end"], $campus["id"], $params["selectSurvey"]);
      }
      elseif($surveyAddon){
        $resultStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      elseif($campus["id"]){
        $resultStmt->bind_param("ssi", $dates["start"], $dates["end"], $campus["id"]);
      }
      else{
        $resultStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $resultStmt->execute();
      $resultStmt->bind_result($type_bind, $count_bind);
      while ($resultStmt->fetch()) {
        $report[$type_bind] = $count_bind;
      }
    }

    return $report;
  }

  function getSurveyHighlights($params){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $campus = getCampus($params);
    $dates = getDates($params);

    $report = array();
    $whereClause = " where ";
    if($params["onlyInt"]){
      $whereClause = " inner join " . DEMOG . " on a.id = " . DEMOG . ".entity_id 
        where " . DEMOG . D_INT . " = \"yes\" and ";
    }
    $surveyAddon = "";
    if($params["selectSurvey"]){
      $surveyAddon = " and civicrm_activity.source_record_id = ? ";
    }
    $highlightQuery = "select count(distinct a.id) as 'TOTAL', count(CASE civicrm_activity.status_id WHEN 2 then 1 ELSE NULL END) as 'COMPLETED',
      count(CASE civicrm_activity.status_id WHEN 3 then 1 ELSE NULL END) as 'IN PROGRESS',
      count(distinct v.id) as 'VOLUNTEERS' from civicrm_activity
      inner join civicrm_activity_target on civicrm_activity.id = civicrm_activity_target.activity_id
      inner join civicrm_contact a on civicrm_activity_target.target_contact_id = a.id 
      left join civicrm_activity_assignment on civicrm_activity.id = civicrm_activity_assignment.activity_id
      left join civicrm_contact v on civicrm_activity_assignment.assignee_contact_id = v.id 
      inner join civicrm_relationship on a.id = civicrm_relationship.contact_id_a
      inner join civicrm_contact b on civicrm_relationship.`contact_id_b` = b.id " . $whereClause . "
      activity_date_time between ? and ? and activity_type_id = 32 " . $campus["query"] . $survyAddon . " and priority_id <> 4";
    if ($highlightStmt = $mysqli->prepare($highlightQuery)){
      if($surveyAddon && $campus["id"]){
        $highlightStmt->bind_param("ssii", $dates["start"], $dates["end"], $campus["id"], $params["selectSurvey"]);
      }
      elseif($surveyAddon){
        $highlightStmt->bind_param("ssi", $dates["start"], $dates["end"], $params["selectSurvey"]);
      }
      elseif($campus["id"]){
        $highlightStmt->bind_param("ssi", $dates["start"], $dates["end"], $campus["id"]);
      }
      else{
        $highlightStmt->bind_param("ss", $dates["start"], $dates["end"]);
      }
      $highlightStmt->execute();
      $highlightStmt->bind_result($total_bind, $completed_bind, $progress_bind, $volunteers_bind);
      while ($highlightStmt->fetch()) {
        $report = array("TOTAL" => $total_bind, "COMPLETED" => $completed_bind,
          "IN PROGRESS" => $progress_bind, "VOLUNTEERS" => $volunteers_bind);
      }
    }

    return $report;
  }



//****************************************************************************************************************

  function getSchools(){
    global $permissions;
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $schools = array();
    $schoolQuery = "select school.`organization_name` as 'SCHOOL', school.`id` as 'ID', school.external_identifier as 'PULSEID' from civicrm_contact school
      inner join civicrm_value_school_info_10 on civicrm_value_school_info_10.entity_id = school.id
      where school.contact_sub_type = 'School' and civicrm_value_school_info_10.do_we_have_a_ministry_presence_h_73 = 'Yes' order by school.organization_name asc;";
    if ($result = $mysqli->query($schoolQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        if(in_array($row["PULSEID"], $permissions["ids"]) || isset($permissions["autoScript"])){
          $schools[$row["ID"]] = $row["SCHOOL"];
        }
      }
    }
    return $schools;
  }

  function getSurveys(){
    $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, CONNECT_DB);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }
    $mysqli->set_charset("utf8");
    $surveys = array();
    $surveyQuery = "select civicrm_survey.title as 'SURVEY', civicrm_survey.id as 'ID' from civicrm_activity
      inner join civicrm_survey on civicrm_activity.source_record_id = civicrm_survey.id
      where civicrm_activity.activity_type_id = 32
      group by `civicrm_survey`.id;";
    if ($result = $mysqli->query($surveyQuery)) {
      while ($row = mysqli_fetch_assoc($result)) {
        $surveys[$row["ID"]] = $row["SURVEY"];
      }
    }
    return $surveys;
  }

?>