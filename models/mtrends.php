<?php
error_reporting('on');
class MTrends extends Model {

	const CACHE_PATH = "/var/www/funkatron.com/ciapp_tss/cache/";
	const CACHE_DURATION = 3600;
	

	function MTrends()
	{
		parent::Model();
	}

	/**
	 * Retrieves source stats for a period of time segmented by days
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
		
		$hashkey = 'TRENDS-'.$startkey.'_to_'.$endkey;
		
		
		/*
			Cache lookup
		*/
		if (($nocache != 'nocache') && file_exists(self::CACHE_PATH."$hashkey")){
			
			$delta = strtotime('now') - filemtime(self::CACHE_PATH."$hashkey");
			if ($delta < self::CACHE_DURATION) {
				// echo "cached";
				$statObjs = json_decode(file_get_contents(self::CACHE_PATH."$hashkey"));
				// echo "<pre>"; echo print_r($statObjs, true); echo "</pre>";
				return $statObjs;
			}
		}
		
		// echo "nocached";
		
		/*
			cache has expired or DNE, so fall through and query
		*/
		$url = "http://127.0.0.1:5984/twitter_trends/_view/rank/byday?group=true&startkey=$startkey&endkey=$endkey";
		// echo "<pre>"; echo print_r($url, true); echo "</pre>";
		$json_counts = file_get_contents($url);
		// echo "<pre>"; echo print_r($json_counts, true); echo "</pre>";
		$result = json_decode($json_counts);

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
	
	
	
	/**
	 * Retrieves source stats for a period of time segmented by hours
	 *
	 * @param string $date_start 
	 * @param int $num_days
	 * @return array
	 * @author Ed Finkler
	 */
	public function getStatsForHourPeriod($datetime_start, $num_hours, $term=null, $nocache=null)
	{
		
		if ($term) { $nocache='nocache'; }
		
		// echo "<pre>"; echo print_r($term, true); echo "</pre>";
		$date_start = strtotime($datetime_start);
		/*
			create start key
		*/
		$Y = date('Y', $date_start);
		$m = date('n', $date_start)-1; // javascript stores months starting at 0
		$d = date('j', $date_start);
		$h = date('G', $date_start);
		if ($term) {
			$startkey = "[$Y,$m,$d,$h,\"$term\"]";
		} else {
			$startkey = "[$Y,$m,$d,$h]";
		}
		
		
		$date_end = $date_start+($num_hours*60*60);
		/*
			create end key
		*/
		$Y = date('Y', $date_end);
		$m = date('n', $date_end)-1; // javascript stores months starting at 0
		$d = date('j', $date_end);
		$h = date('G', $date_end);
		if ($term) {
			$endkey = "[$Y,$m,$d,$h,\"$term\"]";
		} else {
			$endkey = "[$Y,$m,$d,$h]";
		}
		
		if ($term){
			$hashkey = 'TRENDS-'.$startkey.'_to_'.$endkey.'-'.$term;
		} else {
			$hashkey = 'TRENDS-'.$startkey.'_to_'.$endkey;
		}
		
		
		/*
			Cache lookup
		*/
		if (($nocache != 'nocache') && file_exists(self::CACHE_PATH."$hashkey")){
			
			$delta = strtotime('now') - filemtime(self::CACHE_PATH."$hashkey");
			if ($delta < self::CACHE_DURATION) {
				// echo "cached";
				$statObjs = json_decode(file_get_contents(self::CACHE_PATH."$hashkey"));
				// echo "<pre>"; echo print_r($statObjs, true); echo "</pre>";
				return $statObjs;
			}
		}
		
		// echo "nocached";
		
		/*
			cache has expired or DNE, so fall through and query
		*/
		$url = "http://127.0.0.1:5984/twitter_trends/_view/rank/byhour?group=true&startkey=$startkey&endkey=$endkey";
		// echo "<pre>"; echo print_r($url, true); echo "</pre>";
		$json_counts = file_get_contents($url);
		// echo "<pre>"; echo print_r($json_counts, true); echo "</pre>";
		$result = json_decode($json_counts);

		// echo "<pre>"; var_dump($result->rows); echo "</pre>";

		foreach ($result->rows as $row) {
			// echo "<pre>COUNT"; var_dump($row); echo "</pre>";
			if (isset($stats[$row->key[4]])) {
				$stats[$row->key[4]] += $row->value;
			} else {
				$stats[$row->key[4]] = $row->value;
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
	
	
	
	
	
	
	
	
	
	/**
	 * Retrieves source stats for a period of time segmented by hours
	 *
	 * @param string $date_start 
	 * @param int $num_days
	 * @return array
	 * @author Ed Finkler
	 */
	public function getTermStatsForHourPeriod($datetime_start, $num_hours, $term=null, $nocache=null)
	{
		
		if ($term) { $nocache='nocache'; }
		
		$terms = explode(',',$term);
		
		// echo "<pre>"; echo print_r($term, true); echo "</pre>";
		$date_start = strtotime($datetime_start);
		/*
			create start key
		*/
		$Y = date('Y', $date_start);
		$m = date('n', $date_start)-1; // javascript stores months starting at 0
		$d = date('j', $date_start);
		$h = date('G', $date_start);
		$startkey = "[$Y,$m,$d,$h]";
		$unixstart= mktime($h,0,0,$m+1,$d,$Y);
		
		
		$date_end = $date_start+($num_hours*60*60);
		/*
			create end key
		*/
		$Y = date('Y', $date_end);
		$m = date('n', $date_end)-1; // javascript stores months starting at 0
		$d = date('j', $date_end);
		$h = date('G', $date_end);
		$endkey = "[$Y,$m,$d,$h]";
		$unixend= mktime($h,0,0,$m+1,$d,$Y);


		/*
			make array of unixtimes for sample points
		*/
		$samplepoints = array();
		
		for ($x=$unixstart; $x<$unixend; $x += (60*60)) {
			$samplepoints[$x] = 0;
		}
		// echo "<pre>"; echo print_r($samplepoints, true); echo "</pre>";


		if ($term){
			$hashkey = 'TRENDS-'.$startkey.'_to_'.$endkey.'-'.$term;
		} else {
			$hashkey = 'TRENDS-'.$startkey.'_to_'.$endkey;
		}
		
		
		/*
			Cache lookup
		*/
		if (($nocache != 'nocache') && file_exists(self::CACHE_PATH."$hashkey")){
			
			$delta = strtotime('now') - filemtime(self::CACHE_PATH."$hashkey");
			if ($delta < self::CACHE_DURATION) {
				// echo "cached";
				$statObjs = json_decode(file_get_contents(self::CACHE_PATH."$hashkey"));
				// echo "<pre>"; echo print_r($statObjs, true); echo "</pre>";
				return $statObjs;
			}
		}
		
		// echo "nocached";
		
		/*
			cache has expired or DNE, so fall through and query
		*/
		$url = "http://127.0.0.1:5984/twitter_trends/_view/rank/byhour?group=true&startkey=$startkey&endkey=$endkey";
		// echo "<pre>"; echo print_r($url, true); echo "</pre>";
		$json_counts = file_get_contents($url);

		$result = json_decode($json_counts);

		// echo "<pre>"; var_dump($result->rows); echo "</pre>";


		
		foreach($terms as $term) {
			$stats->term = $term;
			$stats->points = $samplepoints;

			foreach ($result->rows as $row) {
				// echo "<pre>"; var_dump($row); echo "</pre>";
				if (strtolower($row->key[4]) == strtolower($term)) {
					$unixhour = strtotime($row->key[0]."-".($row->key[1]+1)."-".$row->key[2].' '.($row->key[3]+1).":00:00");
					// echo date("Y-m-d H:i:s", $unixhour)."<br>";
					$stats->points[$unixhour] = $row->value;
				}
			}
			$termstats[] = $stats;
			unset($stats);
		}
		// echo "<pre>"; var_dump($result); echo "</pre>";
		unset($result);

		
		/*
			Cache this
		*/
		file_put_contents(self::CACHE_PATH."$hashkey", json_encode($statObjs));
		
		

		
		// echo "<pre>"; var_dump($termstats); echo "</pre>";
		
		// echo "<pre>"; echo print_r($stats, true); echo "</pre>";
		
		return $termstats;
	}
	
	
	
	
	
	
	
	
}
