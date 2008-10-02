#!/usr/bin/php
<?php
/*
	get DB data
*/
$url = 'http://127.0.0.1:5984/publictweets/';
$json = file_get_contents($url);
$dbdata = json_decode($json);

$total = $dbdata->doc_count;
$amount = 100;
$startkey = null;

for ($x=$amount;;) {
	echo "<pre>"; echo print_r($x, true); echo "</pre>\n";
	
	$url = 'http://127.0.0.1:5984/publictweets/_all_docs?count='.$x;
	if ($startkey) {
		$url .= '&startkey="'.$startkey.'"';
	}
	
	echo "<pre>"; echo print_r($url, true); echo "</pre>\n";
	
	$json = file_get_contents($url);

	if (!$json) {
		trigger_error("getting public timeline failed -- exiting", E_USER_ERROR);
	}

	$alltweets = json_decode($json);
	
	if (count($alltweets->rows) < 1) {
		exit('done');
	}
	
	/*
		Get key from last element, then reset array pointer
	*/
	$startkey = end($alltweets->rows)->key;
	reset($alltweets->rows);
	
	foreach($alltweets->rows as $doc) {
		$docurl = 'http://127.0.0.1:5984/publictweets/'.$doc->id;
		$docjson = file_get_contents($docurl);
		$tweet = json_decode($docjson);
		
		
		
		// echo "<pre>"; echo print_r($tweet, true); echo "</pre>";
		$newdoc->date = strtotime($tweet->created_at);
		$newdoc->source = $tweet->source;
		$newjson = json_encode($newdoc);
		
		// echo "<pre>"; echo print_r($newdoc, true); echo "</pre>";

		
		
		$cdb_url = 'http://127.0.0.1:5984/publictweets_sources/'.$tweet->id;
		
		echo "<pre>"; echo print_r($cdb_url,true); echo "</pre>\n";
		
		// echo "<pre>"; echo print_r($newjson, true); echo "</pre>";
		// echo "<pre>"; echo print_r(json_encode($newdoc), true); echo "</pre>";
		$http = new HttpRequest($cdb_url, HttpRequest::METH_PUT);
		// $putdata = json_encode($newjson);
		// echo "$putdata\n";
		
		
		$http->setPutData($newjson);
		$http->setContentType('application/json');

		$httpresp = $http->send();
		echo "<pre>Response"; var_dump($http->getRawResponseMessage()); echo "</pre>\n\n\n";
		// sleep(1);
		unset($newdoc);
	}
	

}
