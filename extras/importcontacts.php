<?php
  $_SERVER['DOCUMENT_ROOT'] = "..";
  include '../discover/blackbox.php';
  include '../config/dbconstants.php';
  include '../config/columnnames.php';

  function getInter($value){
    return ($value ? "yes" : "no");
  }

  function getGender($value){
    if($value){
      return ($value == 1 ? 2 : 1);
    }
    return 0;
  }

  $mysqli = new mysqli(CONNECT_HOST, CONNECT_USER, CONNECT_PASSWD, "pulseDisc");
  if (mysqli_connect_errno()) {
    throw new Exception($mysqli->connect_error);
  }
  $mysqli->set_charset("utf8");

  echo "STARTING DISCOVER CONTACT IMPORT\n";
  $discQuery = "select contacts.id as 'CONTACTID', contacts.first_name as 'FIRST', contacts.last_name as 'LAST',
    contacts.email as 'EMAIL', contacts.mobile_phone as 'PHONE', contacts.gender_id as 'GENDER',
    contacts.next_step_id as 'LEVEL', contacts.what_i_am_trusting_god_to_do_next as 'NEXT',
    contacts.international as 'INTER', connect_powertochange_org.civicrm_contact.id as 'CAMPUS',
    cim_hrdb_person.civicrm_id as 'ID', cim_hrdb_person.person_fname as 'FNAME',
    cim_hrdb_person.person_lname as 'LNAME', contacts.created_at as 'DATE' from contacts
    inner join contacts_people on contacts.`id` = contacts_people.`contact_id`
    inner join cim_hrdb_person on contacts_people.`person_id` = cim_hrdb_person.`person_id`
    inner join connect_powertochange_org.civicrm_contact on contacts.campus_id = connect_powertochange_org.civicrm_contact.external_identifier
    order by cim_hrdb_person.civicrm_id;";
  if ($result = $mysqli->query($discQuery)) {
    while ($row = mysqli_fetch_assoc($result)) {
      $userName = $row["FNAME"] . " " . $row["LNAME"] . " (" . $row["ID"] . ")";
      $newContact = array(
        "inputFirst" => $row["FIRST"],
        "inputLast" => $row["LAST"],
        "inputEmail" => $row["EMAIL"],
        "inputPhone" => $row["PHONE"],
        "selectGender" => getGender($row["GENDER"]),
        "selectInter" => getInter($row["INTER"]),
        "selectLevel" => $row["LEVEL"],
        "inputNext" => $row["NEXT"],
        "selectCampus" => $row["CAMPUS"],
        "inputID" => $row["ID"],
        "date" => $row["DATE"]
      );
      //print_r($newContact);

      $cid = new_contact($newContact, true);
      if(is_numeric($cid)){
        echo "SUCCESS: Contact Added ID: " . $cid . " for " . $userName . "\n";
      //$cid = "1";
      //if(true){
        $noteQuery = "select content as 'NOTE', created_at as 'DATE' from notes
          where noteable_type = 'Contact' and noteable_id=" . $row["CONTACTID"];
        if ($notes = $mysqli->query($noteQuery)) {
          while ($note = mysqli_fetch_assoc($notes)) {
            $newNote = array(
              "inputCID" => $cid,
              "inputSubject" => "Imported Pulse Note",
              "inputNote" => $note["NOTE"],
              "date" => $note["DATE"]
            );
            //print_r($newNote);
            $noteReturn = add_note($newNote);
            if($noteReturn["result"] == 1){
              echo "SUCCESS: Note Added for " . $cid . "\n";
            }
            else {
              echo "FAILED: Note error for " . $cid . " (Msg: " . $note["NOTE"] . "): " . $noteReturn["result"];
            }
          }
        }

        $rejQuery = "select activity_type_id as 'TYPE', created_at as 'DATE' from activities
          where reportable_type = 'Contact' and reportable_id=" . $row["CONTACTID"];
        if ($rejoicables = $mysqli->query($rejQuery)) {
          while ($rej = mysqli_fetch_assoc($rejoicables)) {
            $newRej = array(
              "inputID" => $row["ID"],
              "inputCID" => $cid,
              "inputCampus" => $row["CAMPUS"],
              "inputWitness" => $userName,
              "inputType" => $rej["TYPE"],
              "date" => $rej["DATE"]
            );

            if($rej["TYPE"] == 4){
              $decisionParams = array(
                "inputIntegrated" => 0,
                "inputMethod" => 2,
                "inputStory" => "Pulse Import"
              );
              $newRej = array_merge($newRej, $decisionParams);
            }
            //print_r($newRej);
            $rejReturn = add_rejoiceable($newRej);
            if($rejReturn["result"] == 1){
              echo "SUCCESS: Rejoice for " . $cid . "\n";
            }
            else {
              echo "FAILED: Rejoice error for " . $cid . ": " . $rejReturn["result"];
            }
          }
        }
      }
      else {
        echo "Failed Contact: " . $cid;
      }
    }
  }
  echo "ENDING DISCOVER CONTACT IMPORT\n";

?>
