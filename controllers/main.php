<?php
error_reporting('on');
require_once('/var/www/shared/gChart2.php');

class Main extends Controller {

	const JSON_FILE_PATH = '/var/www/funkatron.com/htdocs/twitter-source-stats.json';

	public function Main() {
		parent::Controller();	
	}
	
	public function index() {
		$data = json_decode(file_get_contents(self::JSON_FILE_PATH));
		$rows = $data->rows;
		unset($data);
		
		$last_mod = filemtime(self::JSON_FILE_PATH);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = $last_mod;
		
		$this->load->view('main', $view_data);
	}


	public function today()
	{
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('today',1);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_start']  = 'today';
		$view_data['period_duration'] = 1;

		
		$this->load->view('main', $view_data);
	}	
	
	
	public function yesterday()
	{
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('yesterday',1);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_start']  = 'yesterday';
		$view_data['period_duration'] = 1;
		
		$this->load->view('main', $view_data);
	}

	public function lastsevendays() {
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('-1 week',7);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_start']  = '-1 week';
		$view_data['period_duration'] = 7;
		
		$this->load->view('main', $view_data);
		
	}

	public function lastmonth() {
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('-1 month',30);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		$view_data['period_start']  = '-1 month';
		$view_data['period_duration'] = 30;
		
		$this->load->view('main', $view_data);
		
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
				$labels[] = $key;
				$colors[] = $this->_random_color();
				$subtotal += $val;
			}
		}

		// calculate size of "others"
		$others_val = $total - $subtotal;
		$values[] = $others_val;
		$pers[]   = round(($others_val/$total)*100);
		$labels[] = "Other";
		$colors[] = $this->_random_color();
		
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
	
	
	
	private function _random_color(){
	    mt_srand((double)microtime()*1000000);
	    $c = '';
	    while(strlen($c)<6){
	        $c .= sprintf("%02X", mt_rand(0, 255));
	    }
	    return $c;
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */