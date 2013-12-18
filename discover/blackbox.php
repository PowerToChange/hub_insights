<?php
  include $_SERVER['DOCUMENT_ROOT'].'/config/civi_constants.php';


  function http_call($params){
    $ch = curl_init(RESTURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POST,count($params));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
    $reply = curl_exec($ch);
    if(!$reply){
      throw new Exception(curl_error($ch));
    }
    curl_close($ch);

    return json_decode($reply, TRUE);
  }

  function civicrm_call($entity, $action, $params){
    $allParams = array(
      "api_key" => API_KEY,
      "key" => KEY,
      "entity" => $entity,
      "action" => $action,
      "json" => json_encode($params)
      );

    return http_call($allParams);
  }

  function get_contact($form) {
    global $sends;
    //global $civicrm_id;
    $succeeded = 1;

    $contactParams = array(
      "id" => $form["id"],
      "api.Activity.get" => array(),
      "api.Note.get" => array(),
      "api.CustomValue.get" => array(),
      "api.Relationship.get" => array()
    );
    //$sends[] = $eventParams;

    $contactReturn = civicrm_call("Contact", "get", $contactParams);
    //$sends[] = $eventReturn;
    if ($contactReturn["is_error"] == 1) { $succeeded = $contactReturn["error_message"]; return $succeeded; }

    return $contactReturn;
  }


/*
  function add_decision($form) {
    global $sends;
    global $civicrm_id;
    $succeeded = 1;

    $conParams = array(
      "contact_type" => "Individual",
      "first_name" => $form["inputFirst"]
    );
    if($form["inputCID"]){
      $conParams["id"] = $form["inputCID"];
    }
    if($form["inputLast"]){
      $conParams["last_name"] = $form["inputLast"];
    }
    //$sends[] = $conParams;

    $contact = civicrm_call("Contact", "create", $conParams);
    $sends[] = $contact;
    if ($contact["is_error"] == 1) { $succeeded = $contact["error_message"]; return $succeeded; }
    $id = $contact["id"];
    //var_dump($contact);
    
    $decisionParams = array(
      "source_contact_id" => $civicrm_id,
      "target_contact_id" => $id,
      "assignee_contact_id" => $form["inputCampus"],
      "activity_type_id" => API_D_ID, // rejoiceable
      "subject" => 'Indicated Decision',
      "status_id" => 2,  // completed
      "activity_date_time" => $form["inputDate"],
      "engagement_level" => $form["inputIntegrated"],
      API_D_TYPE => "4",
      API_D_METHOD => $form["inputMethod"],
      API_D_WITNESS => $form["inputWitness"]
    );
    if($form["inputID"]){
      $decisionParams["id"] = $form["inputID"];
    }
    if($form["inputStory"]){
      $decisionParams["details"] = $form["inputStory"];
    }
    //$sends[] = $decisionParams;

    $decisionReturn = civicrm_call("Activity", "create", $decisionParams);
    //$sends[] = $decisionReturn;
    if ($decisionReturn["is_error"] == 1) { $succeeded = $decisionReturn["error_message"]; return $succeeded; }
    //var_dump($surveyReturn);

    return $succeeded;
  }
*/

?>
