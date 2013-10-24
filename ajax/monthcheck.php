<?php
	//echo ($_GET["cid"] ?: "false");
  $result = array("cid" => ($_GET["cid"] ?: "false"), "unrec" => 1, "grow" => 2, "min" => 3, "mult" => 4 );
  echo json_encode($result);
?>