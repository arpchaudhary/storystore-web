<?php
$device_id = isset($_REQUEST["device_id"]) ? $_REQUEST["device_id"] : "";
$acc_token = isset($_REQUEST["acc_token"]) ? $_REQUEST["acc_token"] : "";

if($device_id == ""){
	echo json_encode(array("error" => "device id not found"));
	exit();
}else if ($acc_token == ""){
	echo json_encode(array("error" => "access token not found"));
	exit();
}else{
	echo json_encode(array("id_list" => array("1234","1235","1236","1237","1238","1239")));
	
}

?>
