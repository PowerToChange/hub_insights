<?php
  include 'config/pulse_constants.php';

  //CAS WORK
  include 'CAS/cas_handler.php';
  $user = phpCAS::getAttributes();

  //PERMISSIONS CHECK
  $testIds = array(); $isStaff = false; $validUser = false;
  $url ="https://pulse.p2c.com/api/ministry_involvements?guid=" . $user["ssoGuid"] . "&api_key=" . PULSE_API_KEY;
  $xml = simplexml_load_file($url);
  $civicrm_id = (string) $xml['civicrm_id'];
  foreach ($xml->ministry_involvement as $minInfo) {
    if($minInfo->role[0]['role_id'] != 8){
      $validUser = true;
    }
    if(strcmp($minInfo->role[0]['type'], "StaffRole") == 0){
      $isStaff = true;
    }
    foreach ($minInfo->ministry[0]->campus as $campus){
      $testIds[] = intval($campus['campus_id']);
    }
  }
  $testIds = array_unique($testIds);

  function checkUser($valid){
    if(!$valid){
      echo "<div class=\"alert alert-danger\"><strong>Error!</strong> No Staff Privileges</div>";
      echo "</div></body></html>";
      exit;
    }
  }

?>
