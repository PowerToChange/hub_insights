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

  function delete_event($form) {
    global $sends;
    $succeeded = 2;

    $eventParams = array(
      "id" => $form["inputID"]
    );
    //$sends[] = $eventParams;

    $eventReturn = civicrm_call("Activity", "delete", $eventParams);
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
      API_MON_MULT => $form["inputMult"],
      API_MON_AUTO => $form["inputAuto"]
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

  function get_schools() {
    global $sends;
    //global $civicrm_id;
    $succeeded = 1;

    $contactParams = array(
      "rowCount" => 500,
      "contact_sub_type" => "School",
      "api.CustomValue.get" => array()
    );
    //$sends[] = $eventParams;

    $contactReturn = civicrm_call("Contact", "get", $contactParams);
    //$sends[] = $eventReturn;
    if ($contactReturn["is_error"] == 1) { $succeeded = $contactReturn["error_message"]; return $succeeded; }

    return $contactReturn;
  }

  function get_school($id) {
    global $sends;
    //global $civicrm_id;
    $succeeded = 1;

    $contactParams = array(
      "contact_id" => $id,
      "api.CustomValue.get" => array()
    );

    $contactReturn = civicrm_call("Contact", "get", $contactParams);
    //$sends[] = $eventReturn;
    if ($contactReturn["is_error"] == 1) { $succeeded = $contactReturn["error_message"]; return $succeeded; }

    return $contactReturn;
  }

  function edit_school($params) {
    global $sends;
    $succeeded = 1;

    $checkboxes = array("custom_98", "custom_99", "custom_100", "custom_101", "custom_102", "custom_103", "custom_104", "custom_105", 
      "custom_106", "custom_107", "custom_108", "custom_109", "custom_111", "custom_".API_SCHOOL_MP_13, "custom_".API_SCHOOL_SLM_13);
    foreach($checkboxes as $field){
      if(!isset($params[$field])){
        $params[$field] = 0;
      }
    }

    if($params["phone"]){
      $phoneParams = array("phone" => $params["phone"], "is_primary" => 1);
      if($params["phone_id"]){
        $phoneParams["id"] = $params["phone_id"];
        unset($params["phone_id"]);
      }
      $params["api.phone.create"] = $phoneParams;
      unset($params["phone"]);
    }

    if($params["address_id"]){
      $params["api.address.create"] = array("id" => $params["address_id"], "location_type_id" => 1, "is_primary" => 1, "city" => $params["city"],
        "street_address" => $params["street_address"], "state_province" => $params["state_province"], "postal_code" => $params["postal_code"],
        "country" => $params["country"], "geo_code_1" => $params["geo_code_1"], "geo_code_2" => $params["geo_code_2"]);
    }

    $sends["params"] = $params;

    $contactReturn = civicrm_call("Contact", "create", $params);
    //$sends["error"] = $contactReturn;
    if ($contactReturn["is_error"] == 1) { $succeeded = $contactReturn["error_message"]; return $succeeded; }

    return $succeeded;
  }


?>
