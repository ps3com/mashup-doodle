<?php
require_once("{$CFG->dirroot}/course/format/mashup/framework/Widgeta.php");
require_once("{$CFG->dirroot}/course/format/mashup/classes/MoodleWidget.php");
require_once("{$CFG->dirroot}/course/format/mashup/framework/WookieConnectorService.php");

class InstanceGenerator {

	private $connection = null;
	private $instanceId = null;

	function __construct($instanceId) {
		global $USER,$CFG;
		$this->instanceId = $instanceId;
		$this->connection = new WookieConnectorService ($CFG->mashup_wookie_url, $CFG->mashup_wookie_key, $this->instanceId, $USER->id, $USER->username);
	}
	
	public function getWidgetInstance($persistedWidget){
		global $USER;
		$widgetInstance = $this->getWidget($this->connection, $persistedWidget->getUrl());	
		//set username
		$this->setProperty($widgetInstance, "username", $USER->username, $this->connection);
		return $widgetInstance;
	}

	function getWidget($connection, $widgetIdentifier){
		//global $USER, $COURSE, $CFG;
		$widget = $connection->getOrCreateInstance ($widgetIdentifier);
		return $widget;
	}

	/**
	 * Sets a Personal Property for a Widget instance
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function setProperty($widgetInstance, $key,$value, $connection){

		global $USER, $COURSE,$CFG;
		$newProperty = new Property ( $key, $value );
		$r = $connection->setProperty ( $widgetInstance, $newProperty );
	}

	/**
	 * Adds a participant
	 * NOTE that this should in future call the REST API using POST
	 * but this is currently tricky with Moodle's download_file_content function
	 */
	function addParticipant($widgetinstance, $connection){

		global $USER, $COURSE, $CFG;

		if ($USER->picture != ""){
			$src = $CFG->httpswwwroot.'/user/pix.php/'.$USER->id.'/f1.jpg';
		} else {
			$src = $CFG->httpswwwroot.'/pix/u/f1.png';
		}
		$thisUser = new User ( $USER->id, $USER->username, $src );
		$connection->addParticipant($widgetinstance, $thisUser );
	}
}

?>