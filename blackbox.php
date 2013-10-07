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
    if ($contact["is_error"] == 1) { $succeeded = 2; return $succeeded; }
    $id = $contact["id"];
    //var_dump($contact);
    
    $decisionParams = array(
      "source_contact_id" => $civicrm_id,
      "target_contact_id" => $id,
      "assignee_contact_id" => $form["inputCampus"],
      "activity_type_id" => 47, // rejoiceable
      "subject" => 'Indicated Decision',
      "status_id" => 2,  // completed
      "details" => $form["inputStory"],
      "activity_date_time" => $form["inputDate"],
      "engagement_level" => $form["inputIntegrated"],
      "custom_143" => "4",
      "custom_163" => $form["inputMethod"],
      "custom_171" => $form["inputWitness"]
    );
    if($form["inputID"]){
      $decisionParams["id"] = $form["inputID"];
    }
    //$sends[] = $decisionParams;

    $decisionReturn = civicrm_call("Activity", "create", $decisionParams);
    $sends[] = $decisionReturn;
    if ($decisionReturn["is_error"] == 1) { $succeeded = 2; return $succeeded; }
    //var_dump($surveyReturn);

    return $succeeded;
  }
?>
