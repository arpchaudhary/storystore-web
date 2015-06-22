<?php
require_once('helpers.php');

init();

$device_id = isset($_REQUEST['dev']) ? $_REQUEST['dev'] : NULL ;
$email_id = isset($_REQUEST['mail']) ? $_REQUEST['mail'] : "" ;

if($device_id == NULL){
	publish_err("No device id");
}

$mysql_conn = get_mysql_conn('users');

if($mysql_conn == NULL) {
	publish_err("DB Error");
}

$tbl_name = "user_reg";
$user_id = -1;
//if( $register_query = mysqli_prepare($mysql_conn, "INSERT INTO user_reg(device_id, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE uid = LAST_INSERT_ID(uid) ") ) {
if( $register_query = mysqli_prepare($mysql_conn, "INSERT INTO $tbl_name(device_id, email)  SELECT ?, ? FROM $tbl_name WHERE NOT EXISTS ( SELECT device_id, email FROM $tbl_name WHERE device_id = ? AND email = ? ) LIMIT 1 ") ){
	//bad statement ; should be changed; Parameters dont need to be mapped again
	mysqli_stmt_bind_param($register_query, "ssss", $device_id, $email_id, $device_id, $email_id);
	mysqli_stmt_execute($register_query);
	$user_id =  mysqli_insert_id($mysql_conn);
	
	if($user_id == 0 && $user_id_query = mysqli_prepare($mysql_conn, "SELECT uid FROM user_reg WHERE device_id = ? AND email = ? LIMIT 1") ){
		mysqli_stmt_bind_param($user_id_query, "ss", $device_id, $email_id);
		mysqli_stmt_execute($user_id_query);
		mysqli_bind_result($user_id_query, $user_id);
		mysqli_stmt_fetch($user_id_query);
	} 
}

publish(array("user_id" => $user_id));

close_mysql_conn($mysql_conn);

?>

