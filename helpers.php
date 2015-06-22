<?php
$config = NULL;

function init(){
	date_default_timezone_set('America/Los_Angeles');
	global $config;

	$config = get_config();
	header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
	header('Pragma: no-cache'); // HTTP 1.0.
	header('Expires: 0');
}

function publish_raw($output){
	echo pretty_json($output);
}

function publish($output){
	$ts = new DateTime('now');
	$output['ts'] = $ts -> format('Y-m-d H:i:s');
	$res = json_encode($output);
	publish_raw($res);
	return $res;
}

function publish_err($err_str) {
	//also log such statements in a file defined in the logger
	publish(array("error" => $err_str));
	//Can change the header's if required before sending
	//Probably need to use output buffereing : ob_start and ob_end
	exit();
}

function valid_story_id($story_id){
	return ($story_id != NULL) && (is_numeric($story_id));
}

function redis_story_expiry(){
	global $config;
	if ($config != NULL){
		return $config["redis"]["story_expiry"];
	}
	return 0;
}

function log_debug( $log_line ) {

	$log_file = '/var/log/httpd/con_config.log';
	global $config;
	if($config != NULL)
		$log_file = $config["logger"]["details_php"];
	$time_stamp = new DateTime();
	$log_line = $time_stamp -> format("Y-m-d H:i:s") . $log_line . "\n";
	file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

function is_token_valid($acc_token){
	if($acc_token !=NULL && $acc_token!="")
		return TRUE;
	return FALSE;
}

function get_config(){
	return parse_ini_file(".htmyconfig.ini", true);
}

function get_mysql_conn($db){
	$conn = NULL;
	global $config;
	
	if($config != NULL){
		$conn =  mysqli_connect($config["mysql"]["host"], $config["mysql"]["user"], $config["mysql"]["pass"], $db);
		if(mysqli_connect_errno()){
			return NULL;		
		}
		mysqli_set_charset($conn, "utf8");
	}
	return $conn;
}

function get_redis_conn(){
	require('predis/autoload.php');
	Predis\Autoloader::register();
	$conn = NULL;
	global $config;
	if($config != NULL){
		$conn = new Predis\Client(array(
         		'host' => $config["redis"]["host"],
         	'port' => $config["redis"]["port"],
         	));

		try {
			$conn -> connect();
		} catch(Exception $ex) {
			$conn = NULL;
		}
	}
	return $conn;

}
function show_warning($line){
     echo "<div class=\"alert alert-warning\" role=\"alert\">$line</div>";
}

function show_error($line){
     echo "<div class=\"alert alert-danger\" role=\"alert\">$line</div>";
}

function show_success($line){
     echo "<div class=\"alert alert-success\" role=\"alert\">$line</div>";
}

function close_mysql_conn($conn){
	if($conn != NULL){
		mysqli_close($conn);
	}
}

function pretty_json($json) {
 
    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    //$indentStr   = '&nbsp;&nbsp;';
    $newLine     = "\n";
    //$newLine     = "<br/>";
    $prevChar    = '';
    $outOfQuotes = true;
 
    for ($i=0; $i<=$strLen; $i++) {
 
        // Grab the next character in the string.
        $char = substr($json, $i, 1);
 
        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
 
        // If this character is the end of an element, 
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
 
        // Add the character to the result string.
        $result .= $char;
 
        // If the last character was the beginning of an element, 
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }
 
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
 
        $prevChar = $char;
    }
 
    return $result;
}
?>
