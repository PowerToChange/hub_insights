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

  function add_rejoiceable($form) {
    global $sends;
    $succeeded = 1;

    $rejType = array("1" => "Interaction", "2" => "Spiritual Conversation", 
    "3" => "Gospel Presentation", "4" => "Indicated Decision", "5" => "Shared Spirit-Filled Life");
    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');
    
    $rejoiceParams = array(
      "source_contact_id" => $form["inputID"],
      "target_contact_id" => $form["inputCID"],
      "assignee_contact_id" => $form["inputCampus"],
      "activity_type_id" => API_D_ID, // rejoiceable
      "subject" => $rejType[$form["inputType"]],
      "status_id" => 2,  // completed
      "activity_date_time" => $now,
      API_D_TYPE => $form["inputType"]
    );

    if($form["inputType"] == "4"){
      $decisionParams = array(
        "engagement_level" => $form["inputIntegrated"],
        API_D_METHOD => $form["inputMethod"],
        API_D_WITNESS => $form["inputWitness"]
      );
      if($form["inputStory"]){
        $decisionParams["details"] = $form["inputStory"];
      }
      $rejoiceParams = array_merge($rejoiceParams, $decisionParams);
      $info = $form["inputWitness"] . " (" . $rejType[$form["inputType"]] . ")<br>" . $form["inputStory"];
    }
    else{
      $rejoiceParams["details"] = $form["inputWitness"];
      $info = $form["inputWitness"];
    }

    //$sends[] = $decisionParams;
    $rejoiceReturn = civicrm_call("Activity", "create", $rejoiceParams);
    //$sends[] = $decisionReturn;
    if ($rejoiceReturn["is_error"] == 1) { $succeeded = $rejoiceReturn["error_message"]; return $succeeded; }
    //var_dump($surveyReturn);

    return array("result" => $succeeded, "type" => $rejType[$form["inputType"]], "date" => $now, "info" => $info);
  }

  function add_note($form) {
    global $sends;
    $succeeded = 1;

    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');
    
    $noteParams = array(
      "entity_id" => $form["inputCID"],
      "contact_id" => $form["inputID"],
      "subject" => $form["inputSubject"],
      "note" => $form["inputNote"],
      "modified_date" => $now
    );

    $noteReturn = civicrm_call("Note", "create", $noteParams);
    if ($noteReturn["is_error"] == 1) { $succeeded = $noteReturn["error_message"]; return $succeeded; }

    return array("result" => $succeeded, "note" => $form["inputNote"], "date" => $now, "subject" => $form["inputSubject"]);
  }

?>
