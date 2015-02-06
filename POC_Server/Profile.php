<?php
if (!function_exists('json_decode')) {
    throw new Exception('Livefyre needs the JSON PHP extension.');
}
if (!function_exists('curl_init')) {
  die('This script needs the CURL PHP extension.');
}

require_once('SETTINGS.php');

$result = array("stat" => "error");

try{

	$url = JANRAIN_CAPTURE_URL.'/entity';

	if(isset($_REQUEST["token"]) && $_REQUEST["token"]!="" ){

		// Use the access token to get the user profile data
		$params = array('access_token'    => $_REQUEST["token"],
		                'type_name'       => 'user',
		                'attributes'	  => '["uuid","displayName","givenName","familyName","livefyre_settings","photos","email"]'
		                );

	}elseif(isset($_REQUEST["id"]) && $_REQUEST["id"]!="" ){
		
		// Use the access token to get the user profile data
		$params = array('client_id'      => JANRAIN_CAPTURE_DIRECT_READ_ACCESS_API_CLIENT_ID,
						'client_secret'  => JANRAIN_CAPTURE_DIRECT_READ_ACCESS_API_CLIENT_SECRET,
						'uuid'       	 => $_REQUEST["id"],
		                'type_name'      => 'user',
		                'attributes'	 => '["uuid","displayName","givenName","familyName","livefyre_settings","photos","email"]'
		                );
	
	}else{
		throw new Exception('Missing parameters [token] [id]');
	
	}
	$entityResponse = postToCapture($url, $params);

	if(isset($entityResponse)){
		$decodedEntityResponse = json_decode($entityResponse);
		/*
		echo('<!--'.PHP_EOL);
		var_dump($decodedEntityResponse);
		echo(PHP_EOL);
		echo('/-->'.PHP_EOL);
		*/
		$entityErrors = array();
		$entityResult = new stdClass();
		$entityResult->email_notifications = new stdClass();
		$entityResult->name = new stdClass();
		if(!$decodedEntityResponse || $decodedEntityResponse != "" || !is_null($decodedEntityResponse) )
		{
			if($decodedEntityResponse->stat =="ok"){
///*
//{
//  /* Required:  */
//  "id": "512529b93182977899001bf8",
//  "display_name": "Andy Zhang",
//
//   /* Optional:  */
//  "name":{
//  "first": "Andy",
//  "last": "Zhang",
//  },
//  "email": "andy@livefyre.com",
//  "email_notifications": {
//        "comments": "never",
//        "moderator_comments": "immediately",
//        "moderator_flags": "immediately",
//        "replies": "immediately",
//        "likes": "often"
//  },
//  "autofollow_conversations": "true",
//  "image_url": "http://rack.2.mshcdn.//com/media/ZgkyMDEzLzAyLzI1Lzk0Lzc2NDQxXzU3ODc3LmQyMmE0LmpwZwpwCXRodW1iCTE1MHgxNTAj/22c3d850/2f3/76441_578776734829_8065725_n.jpg",
//  "profile_url": "http://mashable.com/people/512529b93182977899001bf8/",
//  "settings_url": "http://mashable.com/people/512529b93182977899001bf8/edit/",
//  "bio": "WAHOO",
//  "tags": [tag1,tag2],
//  "connections": [ ]
//}
//*/	

		
				if(!isset($decodedEntityResponse->result->uuid) || $decodedEntityResponse->result->uuid =="")
				{
					array_push($entityErrors,"No uuid found");
				}else{
					$entityResult->id = $decodedEntityResponse->result->uuid;
					$entityResult->profile_url = JANRAIN_PUBLIC_PROFILE_URL.$decodedEntityResponse->result->uuid;
					$entityResult->settings_url = JANRAIN_EDIT_PROFILE_URL;
				}
				if(!isset($decodedEntityResponse->result->displayName) || $decodedEntityResponse->result->displayName =="")
				{
					array_push($entityErrors,"No displayName found");
				}else{
					$entityResult->display_name = $decodedEntityResponse->result->displayName;
				}
				if(!isset($decodedEntityResponse->result->email) || $decodedEntityResponse->result->email =="")
				{
					array_push($entityErrors,"No email found");
				}else{
					$entityResult->email = $decodedEntityResponse->result->email;
				}
				if(!isset($decodedEntityResponse->result->givenName) || $decodedEntityResponse->result->givenName =="")
				{
					array_push($entityErrors,"No First Name found");
				}else{
					$entityResult->name->first = $decodedEntityResponse->result->givenName;
				}
				if(!isset($decodedEntityResponse->result->familyName) || $decodedEntityResponse->result->familyName =="")
				{
					array_push($entityErrors,"No Last Name found");
				}else{
					$entityResult->name->last = $decodedEntityResponse->result->familyName;
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_comments) && $decodedEntityResponse->result->livefyre_settings->livefyre_comments !="")
				{
					$entityResult->email_notifications->comments = $decodedEntityResponse->result->livefyre_settings->livefyre_comments;
				}else{
					$entityResult->email_notifications->comments = "";
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_likes) && $decodedEntityResponse->result->livefyre_settings->livefyre_likes !="")
				{
					$entityResult->email_notifications->likes = $decodedEntityResponse->result->livefyre_settings->livefyre_likes;
				}else{
					$entityResult->email_notifications->likes = "";
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_replies) && $decodedEntityResponse->result->livefyre_settings->livefyre_replies !="")
				{
					$entityResult->email_notifications->replies = $decodedEntityResponse->result->livefyre_settings->livefyre_replies;
				}else{
					$entityResult->email_notifications->replies = "";
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_moderator_flags) && $decodedEntityResponse->result->livefyre_settings->livefyre_moderator_flags !="")
				{
					$entityResult->email_notifications->moderator_flags = $decodedEntityResponse->result->livefyre_settings->livefyre_moderator_flags;
				}else{
					$entityResult->email_notifications->moderator_flags = "";
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_moderator_comments) && $decodedEntityResponse->result->livefyre_settings->livefyre_moderator_comments !="")
				{
					$entityResult->email_notifications->moderator_comments = $decodedEntityResponse->result->livefyre_settings->livefyre_moderator_comments;
				}else{
					$entityResult->email_notifications->moderator_comments = "";
				}

				if(isset($decodedEntityResponse->result->livefyre_settings->livefyre_autofollow_conversations) && $decodedEntityResponse->result->livefyre_settings->livefyre_autofollow_conversations !="")
				{
					$entityResult->autofollow_conversations = $decodedEntityResponse->result->livefyre_settings->livefyre_autofollow_conversations;
				}else{
					$entityResult->autofollow_conversations = "";
				}

				if(isset($decodedEntityResponse->result->photos) && count($decodedEntityResponse->result->photos) > 0)
				{
					$entityResult->image_url = $decodedEntityResponse->result->photos[0]->value;
				}else{
					$entityResult->image_url = "";
				}
				
				$result = $entityResult;
				
			}else{
				$result["stat"] = "error";
				$result["message"] = "ERROR: ".$decodedEntityResponse->error."<br />ERROR DESCRIPTION: ".$decodedEntityResponse->error_description;
			}
		}else{
			$result["stat"] = "error";
			$result["message"] = "ERROR: Invalid Entity Response Format";
		}

	}else{
		$result["stat"] = "error";
		$result["message"] = "ERROR: No data returned from entity API";
	}

}catch(Exception $e) {
	$result["stat"] = "error";
	$result["message"] = $e->getMessage();
}


echo json_encode($result);

function postToCapture($url,$postData){
    //Slow down the API Calls
    // 1 Second
    //sleep(1);
    // .5 Second
    //usleep(500000);
    // .25 Second
    // usleep(250000);
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

?>


