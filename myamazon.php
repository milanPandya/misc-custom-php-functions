<?php
/**
* this file is responsible for connecting to amazon Product API services AKA AWS services AKA amazon web services
* @re-work author Milan. Most of code is been adopted from Jaap van Ganswijk (JvG) <ganswijk@xs4all.nl>
* @version 4.0 (since it supports only Amazon web services 4.0)
*/
class amazon
{
	public $query;
	public $parameters;

	// public key	ENTER YOUR PUBLIC KEY HERE
	private $publicKey = "";

	// private key ENTER YOUR PRIVATE KEY HERE
	private $privateKey = "";

	// host url
	private $host = "ecs.amazonaws.com";
	private $uri = "/onca/xml";

	private function connect()
	{
		// some parameters
    	$method = "GET";
    	
		// additional parameters
    	$this->parameters["Service"] = "AWSECommerceService";
    	$this->parameters["AWSAccessKeyId"] = $this->publicKey;
    
		// GMT timestamp
    	$this->parameters["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z",time());  //can't be more than 15 mins late
    
		// API version
    	$this->parameters["Version"] = "2009-03-31";

		ksort($this->parameters);

    	// create query array
    	$array = array();
    	foreach ($this->parameters as $param=>$value)
    	{
        	$param = str_replace("%7E", "~", rawurlencode($param));
        	$value = str_replace("%7E", "~", rawurlencode($value));
        	$array[] = $param."=".$value;
    	}
    	$array = implode("&", $array);

    	// create the string to sign
    	$string_to_sign = $method."\n".$this->host."\n".$this->uri."\n".$array;

    	// calculate HMAC with SHA256 and base64-encoding
    	$signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->privateKey, True));

    	// encode the signature for the request
    	$signature = rawurlencode($signature);

    	// create request
    	$request = "http://".$this->host.$this->uri."?".$array."&Signature=".$signature;

    	return $request;

	}	// connect ends

	public function getResults($query)
	{
		// setting parameters
		$this->parameters = array(
									'Operation'	=> 'ItemSearch',
									'Keywords'	=> urlencode($query),
									'SearchIndex'	=>	'Books',				//searching only for books
									'ItemPage'	=>	1,
									'ResponseGroup'	=>	'Medium'
							);

		$url = $this->connect();
		$results = simplexml_load_string(file_get_contents($url));

		return $results;
	}


}	// class ends

/*
$obj = new amazon();
$results = $obj->getResults('oil');	// searching for oil
print_r($results);
*/
?>
