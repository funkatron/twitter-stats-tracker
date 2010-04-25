<?php
error_reporting('on');
require_once('/var/www/shared/gChart2.php');

class Main extends Controller {

	const JSON_FILE_PATH_LASTHOUR    = 'http://funkatrondigital.com/tss/counts_lasthour.json';
	const JSON_FILE_PATH_LASTDAY     = 'http://funkatrondigital.com/tss/counts_lastday.json';
	const JSON_FILE_PATH_LASTWEEK    = 'http://funkatrondigital.com/tss/counts_lastweek.json';
	const JSON_FILE_PATH_LAST30DAYS  = 'http://funkatrondigital.com/tss/counts_last30days.json';
	const JSON_FILE_PATH_LAST90DAYS  = 'http://funkatrondigital.com/tss/counts_last90days.json';
	const JSON_FILE_PATH_LAST180DAYS = 'http://funkatrondigital.com/tss/counts_last180days.json';
	const JSON_FILE_PATH_ALLTIME     = 'http://funkatrondigital.com/tss/counts_alltime.json';

	const PAGE_TITLE     = 'Twitter Source Stats';

	const HTTP_STATUS_OK = '200 OK';
	const HTTP_STATUS_BAD_REQUEST = '400 Bad Request';
	const HTTP_STATUS_INTERNAL_ERROR = '500 Internal Server Error';
	
	const HTTP_CONTENT_TYPE_JSON = 'Content-Type: application/json';

	public function Main() {
		parent::Controller();	
		$this->output->cache(10);
	}
	
	/**
	 * Returns the current source stats
	 *
	 * @param string $format Either 'html' or 'json'
	 * @return void
	 * @author Ed Finkler	
	 */
	public function index() {
		$this->lasthour();
	}
	
	
	protected function _showData($json_path, $title, $send_json=false)
	{
		if ($send_json) {
			$data = json_decode(file_get_contents($json_path));
			$json = $this->load->view('main_json', array('data' => $data), true);
			$this->output->set_header("HTTP/1.0 ".$status);
			$this->output->set_header("HTTP/1.1 ".$status);
			$this->output->set_header(self::HTTP_CONTENT_TYPE_JSON);
			$this->output->set_output($json);
			return;
		}
		
		$data = json_decode(file_get_contents($json_path));
		
		$view_data['gchart_url'] = $this->_getGchartURL($data);
		$view_data['data'] = $data;
		$view_data['page_title']  = self::PAGE_TITLE.": ".$title;
		
		$this->load->view('main', $view_data);
	}
	
	public function lasthour($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LASTHOUR, 'Last Hour', $format);
	}
	public function lastday($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LASTDAY, 'Last Day', $format);
	}
	public function lastweek($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LASTWEEK, 'Last Week', $format);
	}
	public function last30days($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LAST30DAYS, 'Last 30 Days', $format);
	}
	public function last90days($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LAST90DAYS, 'Last 90 Days', $format);
	}
	public function last180days($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_LAST180DAYS, 'Last 180 Days', $format);
	}
	public function all($format=false)
	{
		$this->_showData(self::JSON_FILE_PATH_ALLTIME, 'All', $format);
	}
	public function sources()
	{
		
	}

	
	
	/**
	 * takes a php structure (an array or object) and serves it as JSON
	 *
	 * @param object $data 
	 * @param string $status 
	 * @return void
	 * @author Ed Finkler
	 */
	private function _sendAsJSON($data, $status = self::HTTP_STATUS_OK) {
		$rsJSON = json_encode($data);
		$this->output->set_header("HTTP/1.0 ".$status);
		$this->output->set_header("HTTP/1.1 ".$status);
		$this->output->set_header(self::HTTP_CONTENT_TYPE_JSON);
		$this->output->set_output($rsJSON);
		return;
	}	
	
	

	
	private function _getGchartURL($data) {
		$total  = $data->total;
		$others_total = 0;
		$values = Array();
		$pers   = Array();
		$labels = Array();
		
		// build data
		foreach($data->results as $result) {
			$per = round(($result->count/$total)*100);
			if ($per >= 1) {
				$values[] = $result->count;
				$pers[] = $per;
				$labels[] = $result->source;
				$colors[] = $this->_random_color();
			} else {
				$others_total += $result->count;
			}
			
		}

		// calculate size of "others"
		$values[] = $others_total;
		$pers[]   = round(($others_total/$total)*100);
		$labels[] = "Other";
		$colors[] = $this->_random_color();
		
		
		$url = $this->_makeGchartUrl($labels, $pers, $colors);
		
		return $url;
	}
	

	
	private function _makeGchartUrl(array $labels, array $values, array $colors) {
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
		foreach($labels as &$label) {
			$label = strip_tags(stripslashes($label));
			if (strlen(utf8_decode($label)) > 20) {
				$label = substr($label, 0, 20)."…";
			}
		}
		$url .= "&chl=".implode('|', $labels);
		
		return $url;
	}
	
	
	
	private function _random_color(){
	    mt_srand((double)microtime()*1000000);
	    $c = '';
	    while(strlen($c)<6){
	        $c .= sprintf("%02X", mt_rand(64, 255));
	    }
	    return $c;
	}
	
}


/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */