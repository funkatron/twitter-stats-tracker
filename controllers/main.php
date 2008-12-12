<?php
error_reporting('on');
require_once('/var/www/shared/gChart2.php');

class Main extends Controller {

	const JSON_FILE_PATH = '/var/www/funkatron.com/htdocs/twitter-source-stats.json';

	const PAGE_TITLE     = 'Twitter Sources';

	const HTTP_STATUS_OK = '200 OK';
	const HTTP_STATUS_BAD_REQUEST = '400 Bad Request';
	const HTTP_STATUS_INTERNAL_ERROR = '500 Internal Server Error';
	
	const HTTP_CONTENT_TYPE_JSON = 'Content-Type: application/json';

	public function Main() {
		parent::Controller();	
	}
	
	/**
	 * Returns the current source stats
	 *
	 * @param string $format Either 'html' or 'json'
	 * @return void
	 * @author Ed Finkler	
	 */
	public function index($format="html") {
		$data = json_decode(file_get_contents(self::JSON_FILE_PATH));
		

		
		$rows = $data->rows;
		unset($data);
		
		$last_mod = filemtime(self::JSON_FILE_PATH);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = $last_mod;
		if (strtolower($format) === "json") {
			
			$this->_sendAsJSON($view_data);
			return;
		}

		$view_data['page_title']  = self::PAGE_TITLE;
		
		$this->load->view('main', $view_data);
	}


	public function today($format="html")
	{
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('today',1);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		if (strtolower($format) === "json") {
			
			$this->_sendAsJSON($view_data);
			return;
		}
		$view_data['period_start']  = 'today';
		$view_data['period_duration'] = 1;
		$view_data['page_title']  = self::PAGE_TITLE;

		
		$this->load->view('main', $view_data);
	}	
	
	
	public function yesterday($format="html")
	{
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('yesterday',1);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		if (strtolower($format) === "json") {
			
			$this->_sendAsJSON($view_data);
			return;
		}
		$view_data['period_start']  = 'yesterday';
		$view_data['period_duration'] = 1;
		$view_data['page_title']  = self::PAGE_TITLE;
		
		$this->load->view('main', $view_data);
	}

	public function lastsevendays($format="html") {
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('-1 week',7);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		if (strtolower($format) === "json") {
			
			$this->_sendAsJSON($view_data);
			return;
		}
		$view_data['period_start']  = '-1 week';
		$view_data['period_duration'] = 7;
		$view_data['page_title']  = self::PAGE_TITLE;
		
		$this->load->view('main', $view_data);
		
	}

	public function lastmonth($format="html") {
		$this->load->model('msources');
		$rows = $this->msources->getStatsForPeriod('-1 month',30);
		
		$view_data = $this->_prepDataForView($rows);
		$view_data['last_updated']  = filemtime(self::JSON_FILE_PATH);
		if (strtolower($format) === "json") {
			
			$this->_sendAsJSON($view_data);
			return;
		}
		$view_data['period_start']  = '-1 month';
		$view_data['period_duration'] = 30;
		$view_data['page_title']  = self::PAGE_TITLE;
		
		$this->load->view('main', $view_data);
		
	}
	
	
	
	/**
	 * takes a php structure (an array or object) and serves it as JSON
	 *
	 * @param object $data 
	 * @param string $status 
	 * @return void
	 * @author Ed Finkler
	 */
	private function _sendAsJSON($data, $status = self::HTTP_STATUS_OK)
	{
		$rsJSON = json_encode($data);
		$this->output->set_header("HTTP/1.0 ".$status);
		$this->output->set_header("HTTP/1.1 ".$status);
		$this->output->set_header(self::HTTP_CONTENT_TYPE_JSON);
		$this->output->set_output($rsJSON);
		return;
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
		$view_data['source_percentages'] = $this->_calcPercentages($view_data);

		
		return $view_data;
	}
	

	private function _calcPercentages($view_data) {
		
		// echo "<pre>"; echo print_r($view_data, true); echo "</pre>";
		
		$view_data['source_percentages'] = array();
		
		foreach ($view_data['source_counts'] as $key=>$val) {
			$per = number_format(($val/$view_data['sources_total'])*100, 3);
			$view_data['source_percentages'][$key] = $per;
		}
		
		return $view_data['source_percentages'];
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