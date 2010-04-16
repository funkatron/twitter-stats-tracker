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
		$this->output->cache(5);
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
	
	
	protected function _showData($json_path, $title)
	{
		$data = json_decode(file_get_contents($json_path));
		

		
		$view_data['gchart_url'] = $this->_getGchartURL($data);
		$view_data['data'] = $data;
		$view_data['page_title']  = self::PAGE_TITLE.": ".$title;
		
		$this->load->view('main', $view_data);
	}
	
	public function lasthour()
	{
		$this->_showData(self::JSON_FILE_PATH_LASTHOUR, 'Last Hour');
	}
	public function lastday()
	{
		$this->_showData(self::JSON_FILE_PATH_LASTDAY, 'Last Day');
	}
	public function lastweek()
	{
		$this->_showData(self::JSON_FILE_PATH_LASTWEEK, 'Last Week');
	}
	public function last30days()
	{
		$this->_showData(self::JSON_FILE_PATH_LAST30DAYS, 'Last 30 Days');
	}
	public function last90days()
	{
		$this->_showData(self::JSON_FILE_PATH_LAST90DAYS, 'Last 90 Days');
	}
	public function last180days()
	{
		$this->_showData(self::JSON_FILE_PATH_LAST180DAYS, 'Last 180 Days');
	}
	public function all()
	{
		$this->_showData(self::JSON_FILE_PATH_ALLTIME, 'All');
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
	private function _sendAsJSON($data, $status = self::HTTP_STATUS_OK)
	{
		$rsJSON = json_encode($data);
		$this->output->set_header("HTTP/1.0 ".$status);
		$this->output->set_header("HTTP/1.1 ".$status);
		$this->output->set_header(self::HTTP_CONTENT_TYPE_JSON);
		$this->output->set_output($rsJSON);
		return;
	}	
	
	

	
	private function _getGchartURL($data)
	{
		$total  = $data->total;
		$others_total = 0;
		$values = Array();
		$pers   = Array();
		$labels = Array();


		// // normalize names (URLs can change, names should not)
		// foreach ($rows as $row) {
		// 	$normkey = strtolower($row->key);
		// 	if (!isset($counts[$normkey])) {
		// 		$counts[$normkey] = 0;
		// 	}
		// 
		// 	$counts[$normkey] += $row->value;
		// }

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
		
		// /*
		// 	make view_data array
		// */
		// $view_data['source_counts'] = $counts;
		// $view_data['sources_total'] = $total;
		// $view_data['chart_url']     = $url;
		// $view_data['source_percentages'] = $this->_calcPercentages($view_data);
		// 
		// 
		// return $view_data;
		
		
		
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
		foreach($labels as &$label) {
			$label = strip_tags(stripslashes($label));
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


/**
 * Debug Function
 * 
 * This function can improve your debugging actions by using the Firefox extension "Firebug"
 * 
 * Copyright (C) 2006 - Mathias Bank (http://forenblogger.de)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 * or go to http://www.gnu.org/licenses/gpl.html
 */

if (!defined("DEBUG_TYPE_LOG")) define("DEBUG_TYPE_LOG",1);
if (!defined("DEBUG_TYPE_INFO")) define("DEBUG_TYPE_INFO",2);
if (!defined("DEBUG_TYPE_WARN")) define("DEBUG_TYPE_WARN",3);
if (!defined("DEBUG_TYPE_ERR")) define("DEBUG_TYPE_ERR",4);

if (!defined("NL")) define("NL","\r\n");

/**
 * Debug function
 * Prints debug messages to firebug
 * 
 * It can be used like:
 * <code>
 *   debug("simple message");
 *   debug("simple warning","",DEBUG_TYPE_WARN);
 *   debug("varX",$x);
 *   debug("object y", $y);
 * </code>
 * @param string text message (names or simple Messages)
 * @param [string] variable which should be printed
 * @param [int] message type: DEBUG_TYPE_LOG | DEBUG_TYPE_INFO | DEBUG_TYPE_WARN | DEBUG_TYPE_ERR
 */
function fbdebug($name, $var="", $messageType=DEBUG_TYPE_LOG) {

	echo '<script type="text/javascript">'.NL;
			
	if ($messageType==DEBUG_TYPE_LOG)
		echo 'console.log("'.$name.'");'.NL;
	elseif ($messageType==DEBUG_TYPE_INFO)
		echo 'console.info("'.$name.'");'.NL;
	elseif ($messageType==DEBUG_TYPE_WARN)
		echo 'console.warn("'.$name.'");'.NL;
	elseif ($messageType==DEBUG_TYPE_ERR)
		echo 'console.error("'.$name.'");'.NL;
	
	if (!empty($var)) {
		if (is_object($var) || is_array($var)) {
			$object = json_encode($var);
			echo 'var object'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' = \''.str_replace("'","\'",$object).'\';'.NL;
			echo 'var val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' = eval("(" + object'.preg_replace('~[^A-Z|0-9]~i',"_",$name).' + ")" );'.NL;
			
			if ($messageType==DEBUG_TYPE_LOG)
				echo 'console.debug(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'.NL;
			elseif ($messageType==DEBUG_TYPE_INFO)
				echo 'console.info(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'.NL;
			elseif ($messageType==DEBUG_TYPE_WARN)
				echo 'console.warn(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'.NL;	
			elseif ($messageType==DEBUG_TYPE_ERR)
				echo 'console.error(val'.preg_replace('~[^A-Z|0-9]~i',"_",$name).');'.NL;	
		} else {
			if ($messageType==DEBUG_TYPE_LOG)
				echo 'console.debug("'.str_replace('"','\"',$var).'");'.NL;
			elseif ($messageType==DEBUG_TYPE_INFO)
				echo 'console.info("'.str_replace('"','\"',$var).'");'.NL;
			elseif ($messageType==DEBUG_TYPE_WARN)
				echo 'console.warn("'.str_replace('"','\"',$var).'");'.NL;	
			elseif ($messageType==DEBUG_TYPE_ERR)
				echo 'console.error("'.str_replace('"','\"',$var).'");'.NL;	
		}
	}
	echo '</script>'.NL;
}



/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */