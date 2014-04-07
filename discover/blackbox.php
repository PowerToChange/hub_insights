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
      "api.Note.get" => array("entity_id" => $form["id"]),
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
    if(isset($form["date"])){
      $now = $form["date"];
    }
    
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
    if(isset($form["date"])){
      $now = $form["date"];
    }
    
    $noteParams = array(
      "entity_id" => $form["inputCID"],
      "contact_id" => $form["inputCID"],
      "subject" => $form["inputSubject"],
      "note" => $form["inputNote"],
      "modified_date" => $now
    );

    if(isset($form["inputNoteID"]) && $form["inputNoteID"]){
      $noteParams["id"] = $form["inputNoteID"];
    }

    $noteReturn = civicrm_call("Note", "create", $noteParams);
    if ($noteReturn["is_error"] == 1) { $succeeded = $noteReturn["error_message"]; return $succeeded; }

    return array("result" => $succeeded, "note" => $form["inputNote"], "date" => $now, "subject" => $form["inputSubject"], "noteid" => $noteReturn["id"]);
  }

  function add_msg($form) {
    global $sends;
    $succeeded = 1;

    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');
    
    $msgParams = array(
      "source_contact_id" => $form["msgID"],
      "target_contact_id" => $form["msgCID"],
      "activity_type_id" => $form["type"], 
      "details" => $form["name"],
      "status_id" => 2,  // completed
      "activity_date_time" => $now
    );

    $msgReturn = civicrm_call("Activity", "create", $msgParams);
    if ($msgReturn["is_error"] == 1) { $succeeded = $msgReturn["error_message"]; return $succeeded; }

    return array("result" => $succeeded, "date" => $now);
  }

  function edit_contact($form){
    global $sends;
    $succeeded = 1;

    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');

    $conParams = array(
      "id" => $form["inputCID"],
      "contact_type" => "Individual",
      "first_name" => $form["inputFirst"],
      "last_name" => $form["inputLast"],
      "gender_id" => $form["selectGender"],
      API_CON_INT => $form["selectInter"],
      API_CON_LEVEL => $form["selectLevel"]
    );
    if($form["inputNext"]){
      $conParams[API_CON_NEXT] = $form["inputNext"];
    }
    if($form["inputEmail"]){
      $emailParams = array("email" => $form["inputEmail"], "is_primary" => 1);
      if($form["emailID"]){
        $emailParams["id"] = $form["emailID"];
      }
      $conParams["api.email.create"] = $emailParams;
    }
    else if($form["emailID"] && !$form["inputEmail"]){
      $conParams["api.email.delete"] = array("id" => $form["emailID"]);
    }
    if($form["inputPhone"]){
      $phoneParams = array("phone" => $form["inputPhone"], "is_primary" => 1);
      if($form["phoneID"]){
        $phoneParams["id"] = $form["phoneID"];
      }
      $conParams["api.phone.create"] = $phoneParams;
    }
    else if($form["phoneID"] && !$form["inputPhone"]){
      $conParams["api.phone.delete"] = array("id" => $form["phoneID"]);
    }

    //$sends[] = $conParams;
    $contact = civicrm_call("Contact", "create", $conParams);
    //$sends[] = $contact;
    if ($contact["is_error"] == 1) { $succeeded = $contact["error_message"]; return $succeeded; }

    if($form["currentCampus"] != $form["selectCampus"] && $succeeded == 1){
      //Change campus
      $oldCampusParams = array(
        "id" => $form["relationshipID"],
        "is_active" => 0,
        "end_date" => $now
      );

      $oldRel = civicrm_call("Relationship", "create", $oldCampusParams);
      if ($oldRel["is_error"] == 1) { $succeeded = $oldRel["error_message"]; return $succeeded; }

      if($succeeded == 1){
        $newCampusParams = array(
          "contact_id_a" => $form["inputCID"],
          "contact_id_b" => $form["selectCampus"],
          "relationship_type_id" => API_REL_CAMPUS,
          "start_date" => $now
        );

        $newRel = civicrm_call("Relationship", "create", $newCampusParams);
        if ($newRel["is_error"] == 1) { $succeeded = $newRel["error_message"]; return $succeeded; }
      }
    }

    return $succeeded;
  }

  function new_contact($form, $import = false){
    global $sends;
    $succeeded = 1;

    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');
    if(isset($form["date"])){
      $now = $form["date"];
    }

    $conParams = array(
      "contact_type" => "Individual",
      "first_name" => $form["inputFirst"],
      "gender_id" => $form["selectGender"],
      API_CON_INT => $form["selectInter"],
      API_CON_LEVEL => $form["selectLevel"]
    );
    if($form["inputNext"]){
      $conParams[API_CON_NEXT] = $form["inputNext"];
    }
    if($form["inputLast"]){
      $conParams["last_name"] = $form["inputLast"];
    }
    if($form["inputEmail"]){
      $conParams["api.email.create"] = array("email" => $form["inputEmail"], "is_primary" => 1);
    }
    if($form["inputPhone"]){
      $conParams["api.phone.create"] = array("phone" => $form["inputPhone"], "is_primary" => 1);
    }

    //$sends[] = $conParams;
    $contact = civicrm_call("Contact", "create", $conParams);
    //$sends[] = $contact;
    if ($contact["is_error"] == 1) { $succeeded = $contact["error_message"]; return $succeeded; }
    $id = $contact["id"];

    $newCampusParams = array(
      "contact_id_a" => $id,
      "contact_id_b" => $form["selectCampus"],
      "relationship_type_id" => API_REL_CAMPUS,
      "start_date" => $now
    );
    $newRel = civicrm_call("Relationship", "create", $newCampusParams);
    if ($newRel["is_error"] == 1) { $succeeded = $newRel["error_message"]; return $succeeded; }

    $relParams = array(
      "contact_id_a" => $form["inputID"],
      "contact_id_b" => $id,
      "relationship_type_id" => API_REL_DISC,
      "is_active" => 1,
      "start_date" => $now
    );
    if($import){
      $relParams["is_active"] = 0;
      $relParams["end_date"] = date('Y-m-d H:i:s');
    }
    $relReturn = civicrm_call("Relationship", "create", $relParams);
    if ($relReturn["is_error"] == 1) { $succeeded = $relReturn["error_message"]; return $succeeded; }
    
    return $id;
  }

  function all_contacts($id, $is_active){
    global $sends;
    $succeeded = 1;

    $params = array(
      "id" => $id,
      "is_active" => $is_active
    );

    $contacts = civicrm_call("DiscoverContacts", "get", $params);
    //$sends[] = $contacts;
    if ($contacts["is_error"] == 1) { $succeeded = $contacts["error_message"]; return $succeeded; }

    return $contacts["values"];
  }

  function change_status($form){
    global $sends;
    $succeeded = 1;

    date_default_timezone_set('America/Toronto');
    $now = date('Y-m-d H:i:s');
    
    if($form["inputActive"]){
      $relParams = array(
        "id" => $form["inputRelID"],
        "is_active" => 0,
        "end_date" => $now
      );
    }
    else {
      $relParams = array(
        "id" => $form["inputRelID"],
        "is_active" => 1,
        "end_date" => "2099-01-01 00:00:00"
      );
    }

    $relReturn = civicrm_call("Relationship", "create", $relParams);
    if ($relReturn["is_error"] == 1) { $succeeded = $relReturn["error_message"]; return $succeeded; }

    return $succeeded;
  }

?>
