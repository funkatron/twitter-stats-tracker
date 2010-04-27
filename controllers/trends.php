<?php
ini_set('display_errors','on');
require_once('/var/www/shared/gChart2.php');

class Trends extends Controller {

	const JSON_FILE_PATH = '/var/www/twittersource.info/htdocs/twitter-source-stats.json';

	const PAGE_TITLE     = 'Twitter Trends';

	public function Trends() {
		parent::Controller();	
	}
	
	public function index() {
		$this->lasthour();
	}



	public function lasthour()
	{
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForHourPeriod('-1 hour',1);
		
		// echo "<pre>"; echo print_r($rows, true); echo "</pre>";
		
		$view_data = $this->_prepDataForView($rows);
		// $view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_interval'] = 'hour';
		$view_data['period_start']  = '-1 hour';
		$view_data['period_duration'] = 1;
		$view_data['page_title']  = self::PAGE_TITLE . ": Last Hour";
		
		
		$this->load->view('trends', $view_data);
	}



	public function last12hours($term=null)
	{
		if ($term) {
			$this->termslast12hours($term);
			return;
		}
		
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForHourPeriod('-12 hours',12,$term);
		
		// echo "<pre>"; echo print_r($rows, true); echo "</pre>";
		
		$view_data = $this->_prepDataForView($rows);
		// $view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_interval'] = 'hour';
		$view_data['period_start']  = '-12 hours';
		$view_data['period_duration'] = 12;
		$view_data['page_title']  = self::PAGE_TITLE . ": Last 12 Hours";
		
		
		$this->load->view('trends', $view_data);
	}


	public function last24hours($term=null)
	{
		if ($term) {
			$this->termslast24hours($term);
			return;
		}
		
		$duration=24;
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForHourPeriod('-12 hours',12,$term);
		
		// echo "<pre>"; echo print_r($rows, true); echo "</pre>";
		
		$view_data = $this->_prepDataForView($rows);
		// $view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_interval'] = 'hour';
		$view_data['period_start']  = '-'.$duration.' hours';
		$view_data['period_duration'] = $duration;
		$view_data['page_title']  = self::PAGE_TITLE . ": Last 24 Hours";
		
		
		$this->load->view('trends', $view_data);
	}







	public function termslast12hours($term=null)
	{
		$duration = 12;
		$this->load->model('mtrends');
		$rows = $this->mtrends->getTermStatsForHourPeriod('-'.$duration.' hours', $duration, $term);
		$this->_showTermStats($rows, '-'.$duration.' hours', $duration);
		
	}


	public function termslast24hours($term=null)
	{
		$duration = 24;
		$this->load->model('mtrends');
		$rows = $this->mtrends->getTermStatsForHourPeriod('-'.$duration.' hours', $duration, $term);
		$this->_showTermStats($rows, '-'.$duration.' hours', $duration);
		
	}


	public function termslast48hours($term=null)
	{
		$duration = 48;
		$this->load->model('mtrends');
		$rows = $this->mtrends->getTermStatsForHourPeriod('-'.$duration.' hours', $duration, $term);
		$this->_showTermStats($rows, '-'.$duration.' hours', $duration);
		
	}





	public function today()
	{
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForPeriod('today',1);
		
		// echo "<pre>"; echo print_r($rows, true); echo "</pre>";
		
		$view_data = $this->_prepDataForView($rows);
		// $view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_start']  = 'today';
		$view_data['period_duration'] = 1;
		$view_data['page_title']  = self::PAGE_TITLE . ": Today";
		
		
		$this->load->view('trends', $view_data);
	}	
	
	
	public function yesterday()
	{
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForPeriod('yesterday',1);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['period_start']  = 'yesterday';
		$view_data['period_duration'] = 1;
		$view_data['page_title']  = self::PAGE_TITLE . ": Yesterday";
		
		$this->load->view('trends', $view_data);
	}

	public function lastsevendays() {
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForPeriod('-7 days',7);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['period_start']  = '-7 days';
		$view_data['period_duration'] = 7;
		$view_data['page_title']  = self::PAGE_TITLE . ": Last Seven Days";
		
		$this->load->view('trends', $view_data);
		
	}

	public function lastmonth() {
		$this->load->model('mtrends');
		$rows = $this->mtrends->getStatsForPeriod('-1 month',30);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['period_start']  = '-1 month';
		$view_data['period_duration'] = 30;
		$view_data['page_title']  = self::PAGE_TITLE . ": Last Month";
		
		$this->load->view('trends', $view_data);
		
	}
	
	
	
	
	private function _showTermStats($rows, $period_start, $num_hours)
	{
		$offset_secs = (int)date('Z');
		
		// build arrays
		$xlabels = array_keys($rows[0]->points);
		
		// echo "<pre>"; echo print_r($xlabels, true); echo "</pre>";
		
		array_shift($xlabels);
		
		
		
		/*
			Adjust for GMT so labels show local server time
		*/
		foreach($xlabels as &$val) {
			$val = $val+$offset_secs;
		}
		
		$maxval = 0;
		foreach($rows as $row) {
			
			if ($maxval < max($row->points)) {
				$maxval = max($row->points);
			}
			
			// echo "<pre>"; echo print_r($maxval, true); echo "</pre>";
			foreach($row->points as &$val) {
				$val = round(($val/$maxval)*100,0);
			}
			
			ksort($row->points);
			array_shift($row->points);
			
			// echo "<pre>"; var_dump($row); echo "</pre>";
			$yvals[]  = implode(",", $row->points);
			$terms[]  = urlencode($row->term);
			$colors[] = $this->_random_color('nowhite');
		}
		

		$url = $this->_makeGchartLineUrl($xlabels, $yvals, $colors, $terms);
		
		/*
			make view_data array
		*/
		// $view_data['source_counts'] = $counts;
		// $view_data['sources_total'] = $total;
		$view_data['chart_url']     = $url;

		
		// $view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_interval'] = 'hour';
		$view_data['period_start']    = $period_start;
		$view_data['period_duration'] = $num_hours;
		$view_data['terms']           = $terms;
		$terms_str = implode(', ', $terms);
		$view_data['page_title']  = self::PAGE_TITLE . " for " . urldecode($terms_str);
		
		
		
		$this->load->view('trends', $view_data);
	}
	
	
	
	private function _prepDataForView(array $rows)
	{
		$total  = 0;
		$subtotal = 0;
		$values = Array();
		$pers   = Array();
		$labels = Array();


		// calc total
		foreach($rows as $row) {
			$total += $row->value;
		}

		// normalize names (URLs can change, names should not)
		foreach ($rows as $row) {
			$normkey = strtolower($row->key);
			if (!isset($counts[$normkey])) {
				$counts[$normkey] = 0;
			}

			$counts[$normkey] += $row->value;
		}
		
		
		// build data
		foreach($counts as $key=>$val) {
			$per = round(($val/$total)*100);
			if ($per >= 1) {
				$values[] = $val;
				$pers[] = $per;
				$labels[] = urlencode($key);
				$colors[] = $this->_random_color();
				$subtotal += $val;
			}
		}
		
		// calculate size of "others"
		$others_val = $total - $subtotal;
		if ($others_val > 0) {
			$values[] = $others_val;
			$pers[]   = round(($others_val/$total)*100);
			$labels[] = "Other";
			$colors[] = $this->_random_color();			
		}
		
		$url = $this->_makeGchartUrl($labels, $pers, $colors);
		
		/*
			make view_data array
		*/
		$view_data['source_counts'] = $counts;
		$view_data['sources_total'] = $total;
		$view_data['chart_url']     = $url;
		
		return $view_data;
	}
	
	
	
	private function _makeGchartUrl(array $labels, array $values, array $colors)
	{
		$url  = "http://chart.apis.google.com/chart?";

		// type
		$url .= "&cht=p3";

		// size
		$url .= "&chs=700x300";

		// data
		$url .= "&chd=t:".implode(',',$values);
		// $url .= "&chds=1,100";

		// colors
		$url .= "&chco=".implode(',', $colors);

		// labels
		$url .= "&chl=".implode('|', $labels);
		
		return $url;
	}
	
	
	
	private function _makeGchartBarUrl(array $xlabels, array $yvals, array $colors, array $terms)
	{
		$max_x = 100;
		$points = count($xlabels);
		// echo "<pre>"; echo print_r($xlabels, true); echo "</pre>";
		// echo "<pre>"; echo print_r($yvals, true); echo "</pre>";
		// echo "<pre>"; echo print_r($terms, true); echo "</pre>";

		$interval = round($max_x/($points-1),0);
		
		$xaxis = array();
		for ($x=0; $x<$points; $x++) {
			if ($interval*$x > $max_x) {
				$xaxis[] = $max_x;
			} else {
				$xaxis[] = $interval*$x;
			}

		}
		$xaxis = implode(',',$xaxis);
		
		
		$url  = "http://chart.apis.google.com/chart?";

		// type
		$url .= "&cht=bvg";

		// size
		$url .= "&chs=700x300";
		$url .= "&chbh=3,0,12";

		// data
		
		foreach($yvals as $yval) {
			$x_y = array();
			// $x_y['x']=$xaxis;
			$x_y['y']=$yval;
			$data_sets[] = implode("|", $x_y);
		}
		$data_string = implode("|", $data_sets);
		$url .= "&chd=t:$data_string";
		
		// $url .= "&chd=t:".implode("|",$yvals);


		// x-axis labels
		$url .= "&chxt=x";
		foreach($xlabels as $unixtime) {
			$xlbls[] = urlencode(date('ga', $unixtime));
		}
		$url .= "&chxl=0:|".implode('|', $xlbls);

		// colors
		$url .= "&chco=".implode(',', $colors);

		// legend
		$url .= "&chdl=".implode('|', $terms);
		$url .= "&chdlp=b";
		
		// gridlines
		echo "<pre>"; echo print_r($interval, true); echo "</pre>";
		$url .= "&chg=".$interval.",10,1,3";
		$url .= "&chf=c,ls,0,FFFFCC,".($interval/100).",FFFFFF,".($interval/100);
		
		// echo "<textarea style='width:95%;height:100px'>$url</textarea>";
		
		return $url;
	}


	private function _makeGchartLineUrl(array $xlabels, array $yvals, array $colors, array $terms)
	{
		
		$max_x = 100;
		$points = count($xlabels);
		
		// foreach($yvals as &$yvalstr) {
		// 	$yvals = explode(',', $yvalstr);
		// 	$yvalstr = $this->array_to_extended_encoding($yvals);
		// }
		
		
		// echo "<pre>"; echo print_r($xlabels, true); echo "</pre>";
		// echo "<pre>"; echo print_r($yvals, true); echo "</pre>";
		// echo "<pre>"; echo print_r($terms, true); echo "</pre>";

		$interval = round($max_x/($points-1),2);
		
		$xaxis = array();
		for ($x=0; $x<$points; $x++) {
			if ($interval*$x > $max_x) {
				$xaxis[] = $max_x;
			} else {
				$xaxis[] = $interval*$x;
			}

		}
		$xaxis = implode(',',$xaxis);
		
		
		$url  = "http://chart.apis.google.com/chart?";

		// type
		$url .= "&cht=lxy";

		// size
		$url .= "&chs=700x300";
		// $url .= "&chbh=10";

		// data
		
		foreach($yvals as $yval) {
			$x_y = array();
			$x_y['x']=$xaxis;
			$x_y['y']=$yval;
			$data_sets[] = implode("|", $x_y);
		}
		$data_string = implode("|", $data_sets);
		$url .= "&chd=t:$data_string";
		
		// $url .= "&chd=t:".implode("|",$yvals);


		// x-axis labels
		$url .= "&chxt=x";
		foreach($xlabels as $unixtime) {
			$xlbls[] = urlencode(date('ga', $unixtime));
		}
		$url .= "&chxl=0:|".implode('|', $xlbls);

		// colors
		$url .= "&chco=".implode(',', $colors);

		// legend
		$url .= "&chdl=".implode('|', $terms);
		$url .= "&chdlp=b";
		
		// gridlines
		$url .= "&chg=".$interval.",10,2,2";
		
		// echo "<textarea style='width:95%;height:100px'>$url</textarea>";
		
		return $url;
	}	
	
	
	
	private function _gchartEncodeExtended($string) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
		$first = floor($value / 64);
        $second = $value % 64;
        $encoding .= $characters[$first] . $characters[$second];
		return $encoding;
	}
	
	
	private function array_to_extended_encoding($array)
	{
	    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';

	    $encoding = '';
	    foreach ($array as $value) {
	        $first = floor($value / 64);
	        $second = $value % 64;
	        $encoding .= $characters[$first] . $characters[$second];
			// echo "<pre>"; echo print_r($value, true); echo "</pre>";
			// echo "<pre>"; echo print_r($encoding, true); echo "</pre>";
	    }

	    return $encoding;
	}
	
	
	
	
	private function _random_color($type='any'){
		
	    mt_srand((double)microtime()*1000000);
	    $c = '';
	    while(strlen($c)<6){
			if ($type == 'dark') {
				$c .= sprintf("%02X", mt_rand(0, 128));
			}
			elseif ($type == 'light') {
				$c .= sprintf("%02X", mt_rand(128, 255));
			}
			elseif ($type == 'nowhite') {
				$c .= sprintf("%02X", mt_rand(0, 223));
			}
			elseif ($type == 'noblack') {
				$c .= sprintf("%02X", mt_rand(32, 255));
			} else {
				$c .= sprintf("%02X", mt_rand(0, 255));
			}
	    }
	    return $c;
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */