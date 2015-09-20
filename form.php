<?php
class usergrid{	
	// $url ;
	// $appname ;
	// $orgname;
	// $clientID ;
	// $secret ;	

	var $base = "https://prod.api.tigo.com/appservices/v1/millicom/fifaleads/";
	var $appname ="fifaleads";
	var $orgname= "millicom";
	var $clientID ="YXA6B8QLoNeeEeOVNomsZR2LHA";
	var $secret ="YXA65dTUyq2nDXxUwxRYntZGRoQj3aY";
	

	function getToken(){
		//file based token cache.
		//try to restore and validate validity
		if(file_exists("token.json")){
			$json = json_decode(file_get_contents("token.json"));
			if ($json->expires > time()){
				$token=$json->access_token;
				return $token;
			}

		}
		if (!isset($token))
		{
		$payload= array("grant_type"=>"client_credentials","client_id"=>$this->clientID,"client_secret"=>$this->secret);
		$resp = $this->sendPost("token",$payload);
		if($resp!== false){
		$token = $resp->access_token;
		file_put_contents("token.json", json_encode(array('access_token'=>$token,"expires"=>time()+$resp->expires_in )));
		}
		
		return $token;
	}else{
		return false;
	}

	}
	function sendPost($url,$data,$token=null,$encode=true){
		$ch = curl_init(); 
	    curl_setopt($ch, CURLOPT_URL, $this->base.$url); 
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    if ($encode)
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	    else
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	    if($token !==null)
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Accept: Application/json',
	    "Authorization: Bearer $token"
	    ));
		$response = json_decode(curl_exec($ch)); 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		curl_close($ch); 
		if($httpCode==200){
			return $response;
		}else return false;
	}

	//TODO store token + exp
	//use it to post
}
$url = "leads";
$ug = new usergrid();
$r=array("status"=>200,"message"=>"success");
$name = isset($_POST['name'])?$_POST['name']:"";
$phone = isset($_POST['phone'])?$_POST['phone']:"";
$email = isset($_POST['email'])?$_POST['email']:"";

if (empty($name) or empty($phone) or empty($email)){
	$r["message"]="error creating user";
	$r['status']=500;
}else{
	$data = array(
		"name"=>time().rand(10,100),
		"client_name"=>"$name",
		"phone"=>"$phone",
		"email"=>"$email",
		"country"=>substr($_SERVER['HTTP_HOST'],-2)
		);
	$token=$ug->getToken();
	$final = $ug->sendPost($url,$data,$token,false);
	if($final === false){
		$r["message"]="error creating user";
		$r['status']=500;
	}
}
header('Content-Type: application/json');
echo json_encode($r);
