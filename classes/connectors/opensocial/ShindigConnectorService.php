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
	
	public function parseMetadataForTitle($url){
		$metadata = $this->getMetadata($url, false);
		if(isset($metadata['error'])){
			if(isset($metadata['error']['message'])){
				$title = 'Error: '.$metadata['error']['message'];
			}
			else{
				$title = 'Error: Unspecified Problem obtaining metadata for '.$url;
			}
		}
		else if(isset($metadata['modulePrefs']['title'])){
			$title = $metadata['modulePrefs']['title'];
		}
		else{
			$title = "unknown gadget";
		}
		return $title;
	}
	
	public function oldGetMetadata($url, $asJson=true) {
		$requestUrl = $this->shindigContextPath.'gadgets/metadata';
		$request = '{
			"context":{"country":"GB","language":"en","view":"default","container":"default"},
			"gadgets":[
			{"url":"'.$url.'","moduleId":1}
			]
			}';
		$response = $this->do_request($requestUrl, $request, 'POST', "Content-Type: application/json-rpc");
		if($response->getStatusCode() != 200){
			throw new Exception("Error: ".$requestUrl);
		}
		$responseAsArray = json_decode($response->getResponseText(), true);
		return $responseAsArray;
	}
		
	public function getMetadata($url, $asJson=true) {
		try {
			/*
			 $requestUrl = $this->shindigContextPath.'gadgets/metadata';
			$request = '{
			"context":{"country":"GB","language":"en","view":"default","container":"default"},
			"gadgets":[
			{"url":"'.$url.'","moduleId":1}
			]
			}';
			*/
			$requestUrl = $this->shindigContextPath.'/rpc';
			//          [{"id":"gadgets.metadata","method":"gadgets.metadata","params":{"groupId":"@self","ids":["http://www.ohloh.net/p/521520/widgets/project_factoids.xml"],"container":"default","userId":"@viewer","fields":["iframeUrls","modulePrefs.*","needsTokenRefresh","userPrefs.*","views.preferredHeight","views.preferredWidth","expireTimeMs","responseTimeMs"]}}]
			$request = '[{"id":"gadgets.metadata","method":"gadgets.metadata","params":{"groupId":"@self","ids":["'.$url.'"],"container":"default","userId":"@viewer","fields":["iframeUrls","modulePrefs.*","needsTokenRefresh","userPrefs.*","views.preferredHeight","views.preferredWidth","expireTimeMs","responseTimeMs"]}}]';					
			if(!$this->checkURL($requestUrl)) {
				throw new Exception("URL for gadget is malformed: ".$requestUrl);
			}
			$response = $this->do_request($requestUrl, $request, 'POST', "Content-Type: application/json-rpc");
			if($response->getStatusCode() != 200){
				throw new Exception("Error: ".$requestUrl);
			}
			$responseAsArray = json_decode($response->getResponseText(), true);
						
			//			///
			//$tmps = $responseAsArray[0]["result"][''.$url.'']['iframeUrls']['default'];
			//$responseAsArray[0]["result"][''.$url.'']['iframeUrls']['default'] = $tmps . '&st=%25st%25';
			
			/////
			$trimmedResponse = $responseAsArray[0]["result"][''.$url.''];
			// rave uses this to show the prefs dialog for a widget, but we'll leave it out for now
			$trimmedResponse['hasPrefsToEdit'] = false;
			//var_dump($trimmedResponse);
			$reEncoded = json_encode($trimmedResponse);
			if($asJson){
				return $reEncoded;
			}
			else{
				return $trimmedResponse;
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
	private function do_request($url, $data, $method = 'POST', $header)
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
  		// add optional headers
		if(isset($header)){
			$params['http']['header'] = $header;
		}
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
		