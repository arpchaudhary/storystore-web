<?php
require_once('helpers.php');

init();

$redis_key = "main_list";
$redis_key_expiry = 86400;
$redis_conn = get_redis_conn();
if($redis_conn != NULL) {
	if($redis_conn -> exists( $redis_key )){
		publish_raw($redis_conn -> get( $redis_key ));
		$redis_conn -> expire( $redis_key , $redis_key_expiry );
		exit();
	}
}

$mysql_conn = get_mysql_conn('stories');

if($mysql_conn == NULL){
	publish_err("Cannot connect to DB");
}

$output = array();
if( $story_query = mysqli_prepare($mysql_conn, "SELECT story_id, MAX(updated_at) FROM story GROUP BY story_id")){
	mysqli_stmt_execute($story_query);
	mysqli_stmt_bind_result($story_query, $db_story_id, $db_updated_at);
	while(mysqli_stmt_fetch($story_query)) {
		$res = array();
		$res["id"] = $db_story_id;
		$res["ts"] = $db_updated_at;
		array_push($output, $res);
	}
	mysqli_stmt_close($story_query);
}
close_mysql_conn($mysql_conn);

$json_res = publish($output);
if($redis_conn != NULL) {
	$redis_conn -> setex($redis_key, $redis_key_expiry, $json_res);
}



?>
