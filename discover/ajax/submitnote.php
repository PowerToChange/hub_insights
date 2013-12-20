<?php
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';

  $result = add_note($_GET);
  //$result = array("result" => 1, "note" => "Fake Note", "date" => "2013-12-19 16:00:00", "subject" => "Fake Subject");
  echo json_encode($result);
?>