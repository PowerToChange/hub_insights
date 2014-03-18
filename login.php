<?php
  include 'config/pulse_constants.php';
  define("STAFF_VIS", 4);
  define("MIN_VIS", 3);
  define("LEADER_VIS", 2);
  define("STUDENT_VIS", 1);

  //CAS WORK
  include 'CAS/cas_handler.php';
  $user = phpCAS::getAttributes();

  //PERMISSIONS CHECK
  $permissions = array("level" => 0, "ids" => array());

  $url ="https://pulse.p2c.com/api/ministry_involvements?guid=" . $user["ssoGuid"] . "&api_key=" . PULSE_API_KEY;
  //$url ="https://pulse.p2c.com/api/ministry_involvements?guid=D0D5195C-9D64-A015-3EDF-6BDC6353BF62&api_key=" . PULSE_API_KEY;

  $xml = simplexml_load_file($url);
  $civicrm_id = (string) $xml['civicrm_id'];
  $pulse_id = (string) $xml['id'];
  foreach ($xml->ministry_involvement as $minInfo) {
    if(strcmp($minInfo->role[0]['type'], "StaffRole") == 0 && $permissions["level"] < STAFF_VIS){
      $permissions["level"] = STAFF_VIS;
    }
    else if(strcmp($minInfo->role[0]['role_id'], "5") == 0 && $permissions["level"] < MIN_VIS){
      $permissions["level"] = MIN_VIS;
    }
    else if(strcmp($minInfo->role[0]['role_id'], "6") == 0 && $permissions["level"] < LEADER_VIS){
      $permissions["level"] = LEADER_VIS;
    }
    else if(strcmp($minInfo->role[0]['type'], "StudentRole") == 0 && $permissions["level"] < STUDENT_VIS){
      $permissions["level"] = STUDENT_VIS;
    }
    foreach ($minInfo->ministry[0]->campus as $campus){
      $permissions["ids"][] = intval($campus['campus_id']);
    }
  }
  $permissions["ids"] = array_unique($permissions["ids"]);

  function userAccess($security_level = MIN_VIS){
    global $permissions;
    return ($permissions["level"] >= $security_level);
  }

  function checkUser($security_level = MIN_VIS){
    if(!userAccess($security_level)){
      echo "<div class=\"alert alert-danger\"><strong>Error!</strong> You don't have permission to view this page.</div>";
      echo "</div></body></html>";
      exit;
    }
  }

?>
