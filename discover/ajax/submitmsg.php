<?php
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';

  $result = add_msg($_GET);
  //$result = array("result" => 1, "date" => "2014-01-20 13:00:00");
  echo json_encode($result);
?>