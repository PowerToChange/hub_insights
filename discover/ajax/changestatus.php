<?php
  include $_SERVER['DOCUMENT_ROOT'].'/discover/blackbox.php';

  $result = change_status($_GET);
  //$result = 1;
  echo json_encode(array("result" => $result));
?>