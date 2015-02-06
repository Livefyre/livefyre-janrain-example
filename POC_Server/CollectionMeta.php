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

	if(isset($_REQUEST["siteId"]) && $_REQUEST["siteId"]!="" && isset($_REQUEST["siteKey"]) && $_REQUEST["siteKey"]!="" ){
		//print_r(get_declared_classes());
		
		$network = Livefyre::getNetwork(LF_NETWORK_NAME, LF_NETWORK_KEY);
		$site = $network->getSite("siteId", "siteKey");
		$collection = $site->buildCommentsCollection("title", "articleId", "url");
		$collection->getData()->setTags("tags");

		$collectionMetaToken = $collection->buildCollectionMetaToken();
		$collectionChecksum = $collection->buildChecksum();

		$result["stat"] = "ok";
		$result["collection_token"] = $collectionMetaToken;
		$result["collection_checksum"] = $collectionChecksum;

	}else{
		$result["stat"] = "error";
		$result["message"] = "Missing required parameters [siteId] [siteKey]";
	}

}catch(Exception $e) {
	$result["stat"] = "error";
	$result["message"] = $e->getMessage();
}


echo json_encode($result);

?>