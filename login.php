<?php
  include 'config/pulse_constants.php';

  //CAS WORK
  include 'CAS/cas_handler.php';
  $user = phpCAS::getAttributes();

  //PERMISSIONS CHECK
  $permissions = array("isStaff" => false, "isStudent" => false, "visibility" => 1, //0 - Admin, 1 - Staff, 2 - Students
    "ids" => array()); //0 - Admin, 1 - Staff, 2 - Students
  $url ="https://pulse.p2c.com/api/ministry_involvements?guid=" . $user["ssoGuid"] . "&api_key=" . PULSE_API_KEY;
  //$url ="https://pulse.p2c.com/api/ministry_involvements?guid=9F6A3C73-A808-8176-F528-C9D695B857BA&api_key=" . PULSE_API_KEY;
  $xml = simplexml_load_file($url);
  $civicrm_id = (string) $xml['civicrm_id'];
  $pulse_id = (string) $xml['id'];
  foreach ($xml->ministry_involvement as $minInfo) {
    if(strcmp($minInfo->role[0]['type'], "StaffRole") == 0){
      $permissions["isStaff"] = true;
    }
    if(strcmp($minInfo->role[0]['role_id'], "5") == 0){
      $permissions["isStaff"] = true;
    }
    if(strcmp($minInfo->role[0]['type'], "StudentRole") == 0){
      $permissions["isStudent"] = true;
    }
    foreach ($minInfo->ministry[0]->campus as $campus){
      $permissions["ids"][] = intval($campus['campus_id']);
    }
  }
  $permissions["ids"] = array_unique($permissions["ids"]);

  function checkUser(){
    global $permissions;
    $validStaff = $permissions["isStaff"] && $permissions["visibility"] >= 1;
    $validStudent = $permissions["isStudent"] && $permissions["visibility"] >= 2;
    if(!($validStaff || $validStudent)){
      echo "<div class=\"alert alert-danger\"><strong>Error!</strong> You don't have permission to view this page.</div>";
      echo "</div></body></html>";
      exit;
    }
  }

?>
