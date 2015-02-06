<?php
if (!function_exists('json_decode')) {
    throw new Exception('Livefyre needs the JSON PHP extension.');
}
//require_once('Requests.php');
require('Livefyre.php');
use Livefyre\Livefyre ;

require_once('SETTINGS.php');

$result = array("stat" => "error");

try{

	if(isset($_REQUEST["uuid"]) && $_REQUEST["uuid"]!="" && isset($_REQUEST["dn"]) && $_REQUEST["dn"]!="" ){
		//print_r(get_declared_classes());
		
		
		$network = Livefyre::getNetwork(LF_NETWORK_NAME, LF_NETWORK_KEY);
		$userAuthToken = $network->buildUserAuthToken($_REQUEST["uuid"], $_REQUEST["dn"], LF_TOKEN_EXPIRATION_SECONDS);
		
		$result["stat"] = "ok";
		$result["lf_token"] = $userAuthToken;

	}else{
		$result["stat"] = "error";
		$result["message"] = $_REQUEST["uuid"]."Missing required parameters [uuid] [dn]";
	}

}catch(Exception $e) {
	$result["stat"] = "error";
	$result["message"] = $e->getMessage();
}


echo json_encode($result);

?>