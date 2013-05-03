<?php
require_once("../../../../../config.php");
require_once("{$CFG->dirroot}/course/format/mashup/framework/WookieConnectorService.php");

class OmdlWebUtils {
	
	private $connection;
	private $w3cWidgets;
	
	function __construct() {
		if(!isset($this->w3cWidgets)){
			$this->getW3CWidgets();
		}
	}
	
	private function getW3CWidgets(){
		global $USER,$CFG;
		$instanceID = "widgets_instance_key";
		$this->connection = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $instanceID, $USER->id );
		$this->w3cWidgets = $this->connection->getAvailableWidgets();
	}
	
	public function doesWidgetExistInWookie($widgetIdentifier){
		foreach ($this->w3cWidgets as $widget){
			if($widget->getIdentifier() == $widgetIdentifier){
				return true;
			}
		}
		return false;
	}
	
	public function getWidgetTitle($widgetIdentifier){
		foreach ($this->w3cWidgets as $widget){
			if($widget->getIdentifier() == $widgetIdentifier){
				return $widget->getTitle();
			}
		}
		return "unnamed widget";
	}
	
	public function downloadAndInstallW3CWidget($widgetUrl){
		global $USER,$CFG;
		try{
			//this will post the widget to wookie & install it
			$response = $this->connection->postWidgetByUrl($widgetUrl, $CFG->mashup_wookie_admin_username, $CFG->mashup_wookie_admin_password);
			return $response;
		}
		catch (Exception $e){
			return null;
		}
	}
}
?>