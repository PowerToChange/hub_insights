<?php
  include 'config/civi_constants.php';

  $postData = array(
    "json" => "1",
    "api_key" => API_KEY,
    "key" => KEY
  );

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
    global $postData;
    $addData = array(
      "entity" => $entity,
      "action" => $action
      );

    $allParams = array_merge($postData, $addData, $params);
    return http_call($allParams);
  }

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
      "activity_type_id" => 47, // rejoiceable
      "subject" => 'Indicated Decision',
      "status_id" => 2,  // completed
      "activity_date_time" => $form["inputDate"],
      "engagement_level" => $form["inputIntegrated"],
      API_TYPE => "4",
      API_METHOD => $form["inputMethod"],
      API_WITNESS => $form["inputWitness"]
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

  function add_event($form) {
    global $sends;
    global $civicrm_id;
    $succeeded = 1;

    $eventParams = array(
      "source_contact_id" => $civicrm_id,
      "target_contact_id" => $form["inputCampus"],
      "activity_type_id" => API_EV_ID, // event
      "subject" => $form["inputName"],
      "status_id" => 2,  // completed
      "activity_date_time" => $form["inputDate"],
      API_EV_TYPE => $form["inputType"],
      API_EV_TOTAL => $form["inputTotal"],
      API_EV_NON => $form["inputNon"]
    );
    if($form["inputID"]){
      $eventParams["id"] = $form["inputID"];
    }
    if($form["inputStory"]){
      $eventParams["details"] = $form["inputStory"];
    }
    //$sends[] = $eventParams;

    $eventReturn = civicrm_call("Activity", "create", $eventParams);
    //$sends[] = $eventReturn;
    if ($eventReturn["is_error"] == 1) { $succeeded = $eventReturn["error_message"]; return $succeeded; }

    return $succeeded;
  }

  function add_monthly($form) {
    global $sends;
    global $civicrm_id;
    $succeeded = 1;

    $monParams = array(
      "source_contact_id" => $civicrm_id,
      "target_contact_id" => $form["inputCampus"],
      "activity_type_id" => API_MON_ID, // event
      "subject" => "Monthly Report",
      "status_id" => 2,  // completed
      "activity_date_time" => $form["inputDate"],
      API_MON_UNREC => $form["inputUnRec"],
      API_MON_GROW => $form["inputGrow"],
      API_MON_MIN => $form["inputMin"],
      API_MON_MULT => $form["inputMult"]
    );
    if($form["inputID"]){
      $monParams["id"] = $form["inputID"];
    }
    //$sends[] = $monParams;

    $monReturn = civicrm_call("Activity", "create", $monParams);
    //$sends[] = $monReturn;
    if ($monReturn["is_error"] == 1) { $succeeded = $monReturn["error_message"]; return $succeeded; }

    return $succeeded;
  }


?>
