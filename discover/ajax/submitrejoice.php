<?php
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';

  $result = add_rejoiceable($_GET);
  //$result = array("result" => 1, "type" => "fake", "date" => "2013-12-19 16:00:00", "info" => "stuff");
  echo json_encode($result);
?>