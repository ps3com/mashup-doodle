<?php
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/Widget.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/w3c/WookieConnectorService.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/connectors/opensocial/ShindigConnectorService.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MoodleWidget.php");

class InstanceGenerator {

	private $wookieconnection = null;
	private $shindigconnection = null;
	private $instanceId = null;

	function __construct($instanceId) {
		global $USER,$CFG;
		$this->instanceId = $instanceId;
		$this->wookieconnection = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $this->instanceId, $USER->id, $USER->username);
		$this->shindigconnection = new ShindigConnectorService($CFG->mashup_shindig_url);
	}
	
	public function getWidgetInstance($persistedWidget){
		global $USER, $CFG;
		if($persistedWidget->getWidgetType() == 1){
			$widgetInstance = $this->getWidget($this->wookieconnection, $persistedWidget->getUrl());	
			//set username
			$this->setProperty($widgetInstance, "username", $USER->username, $this->wookieconnection);
			return $widgetInstance;
		}
		else{
			return $this->shindigconnection->getInstance($persistedWidget);
		}
	}

	function getWidget($wookieconnection, $widgetIdentifier){
		//global $USER, $COURSE, $CFG;
		$widget = $wookieconnection->getOrCreateInstance ($widgetIdentifier);
		return $widget;
	}

	/**
	 * Sets a Personal Property for a Widget instance
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function setProperty($widgetInstance, $key,$value, $wookieconnection){

		global $USER, $COURSE,$CFG;
		$newProperty = new Property ( $key, $value );
		$r = $wookieconnection->setProperty ( $widgetInstance, $newProperty );
	}

	/**
	 * Adds a participant
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function addParticipant($widgetinstance, $wookieconnection){

		global $USER, $COURSE, $CFG;

		if ($USER->picture != ""){
			$src = $CFG->httpswwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
		} else {
			$src = $CFG->httpswwwroot.'/pix/u/f1.png';
		}
		$thisUser = new User ( $USER->id, $USER->username, $src );
		$wookieconnection->addParticipant($widgetinstance, $thisUser );
	}
}

?>