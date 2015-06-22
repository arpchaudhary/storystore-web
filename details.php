<?php

//$start_time = microtime(true);

//Utility helpers for reading configurations and redis and mysql connectors
require_once("helpers.php");

// Call the init function to initialise the basic settings
init();

//Uncomment this to enable error reporting
//error_reporting(-1);
//ini_set('display_errors', 'On');

$id_str = isset( $_REQUEST["id"] ) ? $_REQUEST["id"] : NULL;

$id_arr = array_unique(explode(",", $id_str));

$valid_id_arr = array();
$output = array();
$mysql_con = NULL;


foreach($id_arr as $id){
	if( valid_story_id($id) ) {
		array_push($valid_id_arr, $id);
	} else {
		$output["$id"] = array("error" => "Id Error");
	}
}

//var_dump($valid_id_arr);


if(count($valid_id_arr) > 0 ) {
	
	$story_details = array();			
	$redis_con = get_redis_conn();
	if($redis_con == NULL) {
		log_debug("Redis connection null");
	}
	
	//Now all the valid ids require a response

	foreach($valid_id_arr as $valid_id) {
		$result_found = FALSE;
		if ($redis_con != NULL && $redis_con -> exists( $valid_id )) {
			$story_details = json_decode($redis_con -> get($valid_id), true);
			$result_found = TRUE;
			//array_push($story_details, $cache_output);
			$story_details["cached"] = "yes";
			$redis_con -> expire($valid_id, redis_story_expiry());
		}else {
			if( $mysql_con == NULL )
				$mysql_con = get_mysql_conn( "stories" );

			//echo "Mysql conn is available : ". ($mysql_con == NULL). "<br/>";		
			if( $mysql_con != NULL ) {
				
				if( $story_query = mysqli_prepare($mysql_con, "SELECT * FROM story WHERE story_id = ?") ) {
					mysqli_stmt_bind_param($story_query, "d", $valid_id);
					mysqli_stmt_execute($story_query);
					mysqli_stmt_bind_result($story_query, $db_story_id, $db_cover_pic, $db_title, $db_views, $db_lang, $db_filepath, $db_created_at, $db_updated_at);
					$story_res = array();

					while(mysqli_stmt_fetch($story_query)) {

						//read the file from the path
						$res = array();
						//$output["id"] = $db_story_id;
						$res["language"] = $db_lang;
						$res["title"] = $db_title;
						$res["views"] = $db_views;
						$res["created_at"] = $db_created_at;
						$res["updated_at"] = $db_updated_at;
						$res["cover_pic"] = $db_cover_pic; 
						$res["text"] = file_get_contents($db_filepath);
		
						$result_found = TRUE;
						//Push this result array in the output array object
						array_push($story_res, $res);
					}

					$story_details["data"]["details"] = $story_res;
					mysqli_stmt_close($story_query);
				}

				if($result_found && $category_query = mysqli_prepare($mysql_con, "SELECT category FROM categories WHERE story_id = ?") ){
					mysqli_stmt_bind_param($category_query, "d", $valid_id);
					mysqli_stmt_execute($category_query);
					mysqli_stmt_bind_result($category_query, $db_category);
					$category_res = array();
				
					while(mysqli_stmt_fetch($category_query)) {
						array_push($category_res, $db_category);
					}
					$story_details["data"]["categories"] = $category_res;
					mysqli_stmt_close($category_query);
				}

				if( $result_found && $author_query = mysqli_prepare($mysql_con, "SELECT a.firstname, a.lastname, a.image, a.link, a.description FROM authors AS a INNER JOIN story_authors AS sa WHERE a.author_id = sa.author_id AND sa.story_id = ?") ){
					mysqli_stmt_bind_param($author_query, "d", $valid_id);
					mysqli_stmt_execute($author_query);
					mysqli_stmt_bind_result($author_query, $db_fname, $db_lname, $db_img_link, $db_author_link, $db_author_desc);
					$author_res = array();

					while(mysqli_stmt_fetch($author_query)){
						$res = array();
						$res["firstname"] = $db_fname;
						$res["lastname"] = $db_lname;
						$res["img"] = $db_img_link;
						$res["link"] = $db_author_link;
						$res["desc"] = $db_author_desc;
						array_push($author_res, $res);		
					}
					$story_details["data"]["author"] = $author_res;
					mysqli_stmt_close($author_query);
				}	
			
				if($redis_con != NULL && $result_found){
					$json_res = json_encode($story_details); 
					$redis_con -> setex($valid_id, $config["redis"]["story_expiry"], $json_res);
				}
				$story_details["cached"] = "no";
			} else {
				array_push($story_details, array("error" => "DB Error"));
			}


		}//mysql is null
		
		if($result_found)
			$output[$valid_id]= $story_details;
		else
			$output[$valid_id] = array("error" => "No result");
//		var_dump($output);
	}//foreach valid_id
}

//var_dump($output);
publish($output);

if($mysql_con != null)
	close_mysql_conn( $mysql_con );

//echo "<br/>total time : " . (microtime(true) - $start_time);

?>	
