<?php
require_once('helpers.php');


//check the parameters

$device_id = isset( $_REQUEST["id"] ) ? $_REQUEST["id"] : NULL;
$gcm_token = isset( $_REQUEST["tok"] ) ? $_REQUEST["tok"] : NULL;


if($device_id != NULL && $gcm_token != NULL){
	init();
        $mysql_conn = get_mysql_conn('users');

        if($mysql_conn == NULL){
                publish_err("DB error");
        }

        if( $gcm_query = mysqli_prepare($mysql_conn, "INSERT INTO push(device_id, gcm_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE gcm_id = ?") ) {
                mysqli_stmt_bind_param($gcm_query, 'sss',$device_id, $gcm_token, $gcm_token);
                mysqli_stmt_execute($gcm_query);
                mysqli_stmt_close($gcm_query);
        }
        close_mysql_conn($mysql_conn);

        publish(array("result" => "ok"));
}else{
	publish_err("Invalid Args");
}


?>
