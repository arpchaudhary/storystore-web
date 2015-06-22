<?php
echo "hi";
require_once('/home/ec2-user/arpit-dev/php_files/helpers.php');
echo "hi2";
$output = array();
$acc_token = isset($_REQUEST["acc_token"]) ? $_REQUEST["acc_token"] : "";
$story_id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";

if( is_token_valid($acc_token) ){
	if( $story_id != ""){
		$output = array("1" => "Some comment", "2" => "Another comment");
	} else {
		$output["error"] = "Story id error";
	}
} else {
	$output["error"] = "Access token error";
}

publish($output);
?>
