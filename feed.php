<?php

require_once('helpers.php');
init();

//Write code here to check a valid user id;
//Probably write something in helpers for this

$page_size = 10;

$page_req = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
$mysql_conn = get_mysql_conn( 'stories' );

$start_page = intval($page_req) * $page_size;
$end_page = $start_page + $page_size;

if($mysql_conn == NULL){
	publish_err("DB Error");
}

$output = array();

if( $feed_query = mysqli_prepare($mysql_conn, "SELECT story_id, updated_at FROM feed WHERE available_after < NOW() ORDER BY story_rank DESC LIMIT $start_page, $end_page")) {
	mysqli_stmt_execute($feed_query);
	mysqli_stmt_bind_result($feed_query, $db_story_id, $db_updated_at);

	while(mysqli_stmt_fetch($feed_query)){
		$result = array();
		$result["id"] = $db_story_id;
		$result["updated_at"] = $db_updated_at;
		array_push($output, $result);
	}
}

publish($output);

close_mysql_conn( $mysql_conn );
?>
