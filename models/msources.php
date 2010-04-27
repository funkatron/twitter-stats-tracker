<?php
error_reporting('on');
class MSources extends Model {

	const CACHE_PATH = "/var/www/twittersource.info/ciapp_tss/cache/";
	const CACHE_DURATION = 3600;
	

	function MSources()
	{
		parent::Model();
	}

	/**
	 * Retrieves source stats for a period of time
	 *
	 * @param string $date_start 
	 * @param int $num_days
	 * @return array
	 * @author Ed Finkler
	 */
	public function getStatsForPeriod($date_start, $num_days, $nocache=null)
	{
		$date_start = strtotime($date_start);
		/*
			create start key
		*/
		$Y = date('Y', $date_start);
		$m = date('n', $date_start)-1; // javascript stores months starting at 0
		$d = date('j', $date_start);
		$startkey = "[$Y,$m,$d]";
		
		$date_end = $date_start+($num_days*24*60*60);
		/*
			create end key
		*/
		$Y = date('Y', $date_end);
		$m = date('n', $date_end)-1; // javascript stores months starting at 0
		$d = date('j', $date_end);
		$endkey = "[$Y,$m,$d]";
		
		$hashkey = $startkey.'_to_'.$endkey;
		
		
		/*
			Cache lookup
		*/
		if (($nocache != 'nocache') && file_exists(self::CACHE_PATH."$hashkey")){
			$delta = strtotime('now') - filemtime(self::CACHE_PATH."$hashkey");
			if ($delta < self::CACHE_DURATION) {
				trigger_error('Loading from Cache '.self::CACHE_PATH."$hashkey", E_USER_NOTICE);
				$statObjs = json_decode(file_get_contents(self::CACHE_PATH."$hashkey"));
				if ($statObjs) {
					return $statObjs;
				}
 				
			}
		}
		
		trigger_error('NOT Loading from Cache', E_USER_NOTICE);
		
		/*
			cache has expired or DNE, so fall through and query
		*/
		$url = "http://127.0.0.1:5984/publictweets_sources/_view/sources/byday?group=true&startkey=$startkey&endkey=$endkey";
		// echo "<pre>"; echo print_r($url, true); echo "</pre>";
		$json_counts = file_get_contents($url);
		// echo "<pre>"; echo print_r($json_counts, true); echo "</pre>";
		$result = json_decode($json_counts);
		unset($json_counts);

		// echo "<pre>"; var_dump($result->rows); echo "</pre>";

		foreach ($result->rows as $row) {
			// echo "<pre>COUNT"; var_dump($row); echo "</pre>";
			if (isset($stats[$row->key[3]])) {
				$stats[$row->key[3]] += $row->value;
			} else {
				$stats[$row->key[3]] = $row->value;
			}
		}
		unset($result);

		// echo "<pre>"; echo print_r($stats, true); echo "</pre>";

		foreach ($stats as $key => $value) {
			$obj->key = $key;
			$obj->value = $value;

			$statObjs[] = $obj;
			unset($obj);
		}
		unset($stats);
		
		/*
			Cache this
		*/
		file_put_contents(self::CACHE_PATH."$hashkey", json_encode($statObjs));
		
		

		
		// echo "<pre>"; var_dump($statObjs); echo "</pre>";
		
		// echo "<pre>"; echo print_r($stats, true); echo "</pre>";
		
		return $statObjs;
	}
}
