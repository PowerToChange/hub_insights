<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/config/dbconstants.php';
  include $_SERVER['DOCUMENT_ROOT'].'/config/columnnames.php';

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
        if($permissions["visibility"] >= 1 || in_array($row["PULSEID"], $permissions["ids"])){
          $schools[$row["ID"]] = $row["SCHOOL"];
        }
      }
    }
    return $schools;
  }

?>