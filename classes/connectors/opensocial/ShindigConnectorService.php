<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/HTTP_Response.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/WidgetInstance.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/ShindigConnectorServiceInterface.php");


class ShindigConnectorService implements ShindigConnectorServiceInterface {
	
	private $shindigContextPath;
	private $httpStreamCtx;
	
	function __construct($shindigPath){
		$this->shindigContextPath = $shindigPath;
		$this->setHttpStreamContext(array('http' => array('timeout' => 15)));
	}
	
	public function getInstance($persistedWidget){
		global $USER, $CFG;
		$instance = new WidgetInstance($CFG->mashup_shindig_url."gadgets/ifr?url=".$persistedWidget->getUrl(), "guid", $persistedWidget->getTitle(), 200, 400);
		return $instance;
	}
		
	public function getMetadata($url) {
		try {
			$requestUrl = $this->shindigContextPath.'gadgets/metadata';
			$request = '{
				"context":{"country":"GB","language":"en","view":"default","container":"default"},
				"gadgets":[
				{"url":"'.$url.'","moduleId":1}
				]
			}';
			
			if(!$this->checkURL($requestUrl)) {
				throw new Exception("URL for gadget is malformed: ".$requestUrl);
			}
			$response = $this->do_request($requestUrl, $request);
			if($response->getStatusCode() != 200){
				throw new Exception("Error: ".$requestUrl);
			} 

			$metaData = json_decode($response->getResponseText(), true);
			
			if(isset($metaData["gadgets"][0]["errors"])){
				return "Error:". $metaData["gadgets"][0]["errors"][0];
			}
			else{
				return $metaData["gadgets"][0]["title"];
			}
			
			
		} catch (Exception $e) {
			return "Error:".$e;
		}
	}
	
	private function checkURL($url) {
		
		$UrlCheck = @parse_url($url);
		if($UrlCheck['scheme'] != 'http' || $UrlCheck['host'] == null || $UrlCheck['path'] == null) {
			return false;
		}
		return true;
	}
	
	/** Do HTTP request
	 * @param String url to request
	 * @param String data to send
	 * @param String method to use
	 * @return HTTP_Response new HTTP_Response instance
	 */
	private function do_request($url, $data, $method = 'POST')
	{
		if(is_array($data)) {
			// convert variables array to string:
			$_data = array();
			while(list($n,$v) = each($data)){
				$_data[] = urlencode($n)."=".urlencode($v);
			}
			$data = implode('&', $_data);
		}
		$params = array('http' => array(
				'method' => $method,
				'content' => $data,
				'timeout' => 30
		));
		//}
		$this->setHttpStreamContext($params);
		$response = @file_get_contents($url, false, $this->getHttpStreamContext());
	
		//revert back to default value for other requests
		$this->setHttpStreamContext(array('http' => array('timeout' => 15)));
	
		return new HTTP_Response($response, $http_response_header);
	}
	
	
	/** Set HttpStreamContext parameters
	 *
	 * @param Array array of context parameters
	 */
	private function setHttpStreamContext($params) {
		$this->httpStreamCtx = @stream_context_create($params);
	}
	
	/** Get HttpStreamContext
	 * @return StreamContextResource HttpStreamContext resource
	 */
	
	private function getHttpStreamContext() {
		return $this->httpStreamCtx;
	}
	
	
}
		