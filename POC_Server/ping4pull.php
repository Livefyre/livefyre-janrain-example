<?php
	$root = $_SERVER['DOCUMENT_ROOT'];

	require_once $root."/vendor/autoload.php";
	use Livefyre\Livefyre;

	$network = Livefyre::getNetwork("client-solutions-janrain.fyre.co", "JWoQdZ4ZpKxMYd2qVyEYCw+Oimo=");
	$userAuthToken = $network->buildLivefyreToken();

	$uuid = $_POST["uuid"];

	//set POST variables
	$url = 'https://client-solutions-janrain.quill.fyre.co/api/v3_0/user/'.$uuid.'/refresh';
	$fields_string = 'lftoken='.urlencode($userAuthToken);
	//open connection
	$ch = curl_init();
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//execute post
	$result = curl_exec($ch);
	//close connection
	curl_close($ch);
?>