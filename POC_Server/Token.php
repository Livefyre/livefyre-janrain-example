<?php
if (!function_exists('json_decode')) {
    throw new Exception('Livefyre needs the JSON PHP extension.');
}

require('Livefyre.php');
use Livefyre\Livefyre ;
require_once('SETTINGS.php');

$result = array("stat" => "error");

try {
    $url = JANRAIN_CAPTURE_URL.'/entity';
    
    if(isset($_REQUEST["token"]) && $_REQUEST["token"]!= "") {
        // Use the access token to get the user profile data
        $params = array('access_token'    => $_REQUEST["token"],
                        'type_name'       => 'user',
                        'attributes'   => '["uuid","displayName"]'
                        );
        $entityResponse = postToCapture($url, $params);
        
        if(isset($entityResponse)){
            $decodedEntityResponse = json_decode($entityResponse);
            $entityErrors = array();
            $entityResult->name = new stdClass();
            if(!$decodedEntityResponse || $decodedEntityResponse != "" || !is_null($decodedEntityResponse)) {
                if($decodedEntityResponse->stat =="ok") {
                    if(!isset($decodedEntityResponse->result->uuid) || $decodedEntityResponse->result->uuid =="") {
                        array_push($entityErrors,"No uuid found");
                    } else {
                        $uuid = $decodedEntityResponse->result->uuid;
                    }
                    if(!isset($decodedEntityResponse->result->displayName) || $decodedEntityResponse->result->displayName == "") {
                        array_push($entityErrors,"No displayName found");
                    } else {
                        $dn= $decodedEntityResponse->result->displayName;
                    }
                    
                    $network = Livefyre::getNetwork(LF_NETWORK_NAME, LF_NETWORK_KEY);
                    $userAuthToken = $network->buildUserAuthToken($uuid, $dn, LF_TOKEN_EXPIRATION_SECONDS);
                    
                    $result["stat"] = "ok";
                    $result["lf_token"] = $userAuthToken;
                    
                } else {
                    $result["stat"] = "error";
                    $result["message"] = "ERROR: ".$decodedEntityResponse->error."<br />ERROR DESCRIPTION:".$decodedEntityResponse->error_description;
                }
            } else {
                $result["stat"] = "error";
		$result["message"] = "ERROR: Invalid Entity Response Format";
            }
        } else {
            $result["stat"] = "error";
            $result["message"] = "ERROR: No data returned from entity API";
        }
    } else {
        $result["stat"] = "error";
        $result["message"] = "Missing required parameters [token]";
    }
} catch(Exception $e) {
    $result["stat"] = "error";
    $result["message"] = $e->getMessage();
}

echo json_encode($result);

function postToCapture($url,$postData){
    $result = "";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $result = curl_error($ch);
    } else {
        curl_close($ch);
        $result = $response;
    }
    return $result;
}
